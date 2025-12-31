<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\FinalBudget;
use App\Services\EVMCalculationService;
use Carbon\Carbon;

class FinalBudgetController extends Controller
{
    protected EVMCalculationService $evmService;

    public function __construct(EVMCalculationService $evmService)
    {
        $this->evmService = $evmService;
    }

    public function show(Project $project)
    {
        $project->load(['tasks' => function ($query) {
            $query->orderBy('id', 'asc');
        }, 'tasks.monthlyActualCosts']);
        
        // 1. Get Project Months
        $budgetService = app(\App\Services\MonthlyBudgetService::class);
        $months = $budgetService->getProjectMonths($project);
        
        // 2. Prepare Task Data
        $tasksData = [];
        $totals = [
            'planned' => 0,
        ];
        
        // Initialize monthly totals
        $monthlyTotals = [];
        foreach ($months as $m) {
            $monthlyTotals[$m['key']] = [
                'planned' => 0,
                'actual' => 0,
                'ev_nominal' => 0, // Incremental EV in currency
            ];
        }

        foreach ($project->tasks as $task) {
            $taskMonthlyBudget = $budgetService->calculateMonthlyBudget($task);
            $taskActuals = $task->monthlyActualCosts->keyBy('month');
            
            $row = [
                'task' => $task,
                'monthly' => [],
                'total_planned' => $task->cost,
            ];

            foreach ($months as $m) {
                $monthKey = $m['key'];
                
                // Planned
                $planned = $taskMonthlyBudget[$monthKey] ?? 0;
                
                // Actual & EV
                $actualRecord = $taskActuals->get($monthKey);
                $actual = $actualRecord ? $actualRecord->actual_cost : 0;
                $evPct = $actualRecord ? $actualRecord->earned_value_percentage : 0;
                
                // Calculate Incremental EV (assuming evPct is incremental for that month as per input logic)
                // If evPct represents the % of the total budget "earned" in this specific month:
                $evNominal = ($evPct / 100) * $task->cost;

                $row['monthly'][$monthKey] = [
                    'planned' => $planned,
                    'actual' => $actual,
                    'ev_pct' => $evPct,
                    'ev_nominal' => $evNominal,
                ];

                // Aggregate Totals
                if ($actualRecord) {
                    $monthlyTotals[$monthKey]['planned'] += $planned;
                }
                $monthlyTotals[$monthKey]['actual'] += $actual;
                $monthlyTotals[$monthKey]['ev_nominal'] += $evNominal;
            }
            
            $tasksData[] = $row;
            $totals['planned'] += $task->cost;
        }

        // 3. Calculate Cumulative and Indices for Footer
        $footerData = [];
        $cumPV = 0;
        $cumAC = 0;
        $cumEV = 0;
        
        $bac = $totals['planned']; // Budget At Completion

        foreach ($months as $m) {
            $key = $m['key'];
            $stats = $monthlyTotals[$key];
            
            // Increment cumulatives
            $cumPV += $stats['planned'];
            $cumAC += $stats['actual'];
            $cumEV += $stats['ev_nominal'];

            // PV % (Cumulative PV / Total Budget)
            $pvPct = $bac > 0 ? ($cumPV / $bac) * 100 : 0;

            // EV % (Cumulative EV / Total Budget)
            $evPct = $bac > 0 ? ($cumEV / $bac) * 100 : 0;

            // Indices
            $cv = $cumEV - $cumAC;
            $sv = $cumEV - $cumPV;
            $cpi = $cumAC > 0 ? $cumEV / $cumAC : 0;
            $spi = $cumPV > 0 ? $cumEV / $cumPV : 0;

            // Forecasts
            // EAC = BAC / CPI
            $eac = $cpi > 0 ? $bac / $cpi : $bac; // simplified fallback
            
            // Est Duration (simplified: Total Duration / SPI)
            // Need project duration.
            // Let's assume passed months count? Or total project months?
            // "Time / SPI" usually means Time Elapsed / SPI = Est Time Needed?
            // Or Total Planned Duration / SPI = Est Total Duration.
            // Let's use Total Project Duration (months count)
            $totalDuration = count($months);
            $estDuration = $spi > 0 ? $totalDuration / $spi : 0;

            $footerData[$key] = [
                'pv_incremental' => $stats['planned'],
                'pv_cumulative' => $cumPV,
                'pv_pct' => $pvPct,
                
                'ac_incremental' => $stats['actual'],
                'ac_cumulative' => $cumAC,
                
                'ev_pct_cumulative' => $evPct,
                'ev_cumulative' => $cumEV,
                
                'cv' => $cv,
                'cpi' => $cpi,
                'sv' => $sv,
                'spi' => $spi,
                
                'eac' => $eac,
                'est_duration' => $estDuration,
            ];
        }

        return view('budgets.final', [
            'project' => $project,
            'months' => $months,
            'tasksData' => $tasksData,
            'totals' => $totals,
            'footerData' => $footerData,
            'bac' => $bac,
        ]);
    }
}
