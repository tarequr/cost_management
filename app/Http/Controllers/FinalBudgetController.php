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

    public function calculate(Project $project, \Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'progress' => 'required|numeric|min:0|max:100',
            'actual_cost' => 'required|numeric|min:0',
            'month' => 'required|string', // Format: YYYY-MM
        ]);

        $progress = (float)$validated['progress'];
        $ac = (float)$validated['actual_cost'];
        $selectedMonth = $validated['month'];

        // 1. Calculate PV for the selected month (incremental)
        $budgetService = app(\App\Services\MonthlyBudgetService::class);
        $pv = 0;

        foreach ($project->tasks as $task) {
            // Only include finalized tasks
            if ($task->monthlyActualCosts()->exists()) {
                $taskMonthlyBudget = $budgetService->calculateMonthlyBudget($task);
                $pv += $taskMonthlyBudget[$selectedMonth] ?? 0;
            }
        }

        // 2. EV = Progress % * AC (User's specific formula)
        $ev = ($progress / 100) * $ac;

        // 3. SV = EV - PV
        $sv = $ev - $pv;

        // 4. CV = EV - AC
        $cv = $ev - $ac;

        // 5. Indices (CPI, SPI)
        $cpi = $ac > 0 ? $ev / $ac : 0;
        $spi = $pv > 0 ? $ev / $pv : 0;

        // 6. Total Project BAC
        $bac = $project->tasks->sum('cost');

        // 7. Forecasts
        $eac = $cpi > 0 ? $bac / $cpi : $bac;
        
        // Est Duration (Total Project Months / SPI)
        $months = $budgetService->getProjectMonths($project);
        $totalDuration = count($months);
        $estDuration = $spi > 0 ? $totalDuration / $spi : $totalDuration;

        $fromMonth = count($months) > 0 ? $months[0]['label'] : 'N/A';
        $toMonth = count($months) > 0 ? $months[count($months) - 1]['label'] : 'N/A';

        // Prepare Chart Data
        $chartLabels = [];
        $chartPV = [];
        $chartAC = [];
        $chartEV = [];
        $chartCV = [];
        $chartSV = [];

        $cumPV = 0;
        $cumAC = 0;
        $cumEV = 0;

        $hasReachedSelected = false;

        foreach ($months as $m) {
            $monthKey = $m['key'];
            $chartLabels[] = $m['label'];

            // Calculate incremental values for this month
            if ($monthKey === $selectedMonth) {
                $incPV = $pv; 
                $incAC = $ac; 
                $incEV = $ev;
                $hasReachedSelected = true;
            } else {
                $incPV = 0;
                $incAC = 0;
                $incEV = 0;

                foreach ($project->tasks as $task) {
                    if ($task->monthlyActualCosts()->exists()) {
                        $taskMonthlyBudget = $budgetService->calculateMonthlyBudget($task);
                        $incPV += $taskMonthlyBudget[$monthKey] ?? 0;

                        if (!$hasReachedSelected) {
                            $actualRecord = $task->monthlyActualCosts()->where('month', $monthKey)->first();
                            if ($actualRecord) {
                                $incAC += $actualRecord->actual_cost;
                                $incEV += ($actualRecord->earned_value_percentage / 100) * $task->cost;
                            }
                        }
                    }
                }
            }

            $cumPV += $incPV;
            $chartPV[] = $cumPV;

            if (!$hasReachedSelected || $monthKey === $selectedMonth) {
                $cumAC += $incAC;
                $cumEV += $incEV;
                $chartAC[] = $cumAC;
                $chartEV[] = $cumEV;
                
                // Incremental Variances for the bar chart
                $chartCV[] = $incEV - $incAC;
                $chartSV[] = $incEV - $incPV;
            } else {
                $chartAC[] = null;
                $chartEV[] = null;
                $chartCV[] = null;
                $chartSV[] = null;
            }
        }

        return view('budgets.calculator', [
            'project' => $project,
            'fromMonth' => $fromMonth,
            'toMonth' => $toMonth,
            'pv' => $pv,
            'ac' => $ac,
            'ev' => $ev,
            'sv' => $sv,
            'cv' => $cv,
            'cpi' => $cpi,
            'spi' => $spi,
            'bac' => $bac,
            'eac' => $eac,
            'est_duration' => $estDuration,
            'progress' => $progress,
            'selectedMonth' => $selectedMonth,
            'chartLabels' => $chartLabels,
            'chartPV' => $chartPV,
            'chartAC' => $chartAC,
            'chartEV' => $chartEV,
            'chartCV' => $chartCV,
            'chartSV' => $chartSV,
        ]);
    }
}
