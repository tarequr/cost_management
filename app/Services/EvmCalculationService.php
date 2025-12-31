<?php

namespace App\Services;

use App\Models\Project;
use Carbon\Carbon;

class EVMCalculationService
{
    protected MonthlyBudgetService $budgetService;

    public function __construct(MonthlyBudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    /**
     * Calculate all EVM metrics for a project up to a specific month
     */
    public function calculateEVM(Project $project, string $asOfMonth = null): array
    {
        $asOfMonth = $asOfMonth ?? Carbon::now()->format('Y-m');
        
        // Calculate BAC (Budget at Completion) - Total project budget
        $BAC = $project->tasks->sum('amount');

        // Calculate PV (Planned Value) - Budget for work scheduled up to asOfMonth
        $PV = $this->calculatePlannedValue($project, $asOfMonth);

        // Calculate AC (Actual Cost) - Actual cost incurred up to asOfMonth
        $AC = $this->calculateActualCost($project, $asOfMonth);

        // Calculate EV (Earned Value) - Budget for work completed up to asOfMonth
        $EV = $this->calculateEarnedValue($project, $asOfMonth);

        // Calculate variances
        $SV = $EV - $PV; // Schedule Variance
        $CV = $EV - $AC; // Cost Variance

        // Calculate performance indices
        $SPI = $PV > 0 ? round($EV / $PV, 2) : 0; // Schedule Performance Index
        $CPI = $AC > 0 ? round($EV / $AC, 2) : 0; // Cost Performance Index

        // Calculate forecasts
        $ETC = $CPI > 0 ? round(($BAC - $EV) / $CPI, 2) : 0; // Estimate to Complete
        $EAC = $AC + $ETC; // Estimate at Completion

        return [
            'BAC' => round($BAC, 2),
            'PV' => round($PV, 2),
            'AC' => round($AC, 2),
            'EV' => round($EV, 2),
            'SV' => round($SV, 2),
            'CV' => round($CV, 2),
            'SPI' => $SPI,
            'CPI' => $CPI,
            'ETC' => $ETC,
            'EAC' => round($EAC, 2),
            'as_of_month' => $asOfMonth,
        ];
    }

    /**
     * Calculate Planned Value (PV) up to a specific month
     */
    protected function calculatePlannedValue(Project $project, string $asOfMonth): float
    {
        $PV = 0;

        foreach ($project->tasks as $task) {
            $monthlyBudget = $this->budgetService->calculateMonthlyBudget($task);
            
            foreach ($monthlyBudget as $month => $amount) {
                if ($month <= $asOfMonth) {
                    $PV += $amount;
                }
            }
        }

        return $PV;
    }

    /**
     * Calculate Actual Cost (AC) up to a specific month
     */
    protected function calculateActualCost(Project $project, string $asOfMonth): float
    {
        $AC = 0;

        foreach ($project->tasks as $task) {
            $actualCosts = $task->monthlyActualCosts()
                ->where('month', '<=', $asOfMonth)
                ->get();
            
            $AC += $actualCosts->sum('actual_cost');
        }

        return $AC;
    }

    /**
     * Calculate Earned Value (EV) up to a specific month
     */
    protected function calculateEarnedValue(Project $project, string $asOfMonth): float
    {
        $EV = 0;

        foreach ($project->tasks as $task) {
            $actualCosts = $task->monthlyActualCosts()
                ->where('month', '<=', $asOfMonth)
                ->get();
            
            foreach ($actualCosts as $cost) {
                // EV = (% Complete) × Task Budget
                $monthlyEV = ($cost->earned_value_percentage / 100) * $task->amount;
                $EV += $monthlyEV;
            }
        }

        return $EV;
    }

    /**
     * Get monthly EVM data for chart display
     */
    public function getMonthlyEVMData(Project $project): array
    {
        $months = $this->budgetService->getProjectMonths($project);
        $monthlyData = [];

        $cumulativePV = 0;
        $cumulativeAC = 0;
        $cumulativeEV = 0;

        foreach ($months as $monthInfo) {
            $month = $monthInfo['key'];
            
            // Calculate for this month
            $evm = $this->calculateEVM($project, $month);
            
            $monthlyData[] = [
                'month' => $monthInfo['label'],
                'month_key' => $month,
                'PV' => $evm['PV'],
                'AC' => $evm['AC'],
                'EV' => $evm['EV'],
                'SV' => $evm['SV'],
                'CV' => $evm['CV'],
                'SPI' => $evm['SPI'],
                'CPI' => $evm['CPI'],
            ];
        }

        return $monthlyData;
    }

    /**
     * Get performance status based on SPI and CPI
     */
    public function getPerformanceStatus(float $index): array
    {
        if ($index >= 1.0) {
            return ['status' => 'good', 'color' => 'success', 'icon' => 'check-circle'];
        } elseif ($index >= 0.8) {
            return ['status' => 'warning', 'color' => 'warning', 'icon' => 'exclamation-triangle'];
        } else {
            return ['status' => 'critical', 'color' => 'danger', 'icon' => 'times-circle'];
        }
    }
}
