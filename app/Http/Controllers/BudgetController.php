<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\MonthlyActualCost;
use App\Services\MonthlyBudgetService;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    protected MonthlyBudgetService $budgetService;

    public function __construct(MonthlyBudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    public function draft(Project $project)
    {
        $project->load(['tasks' => function ($query) {
            $query->orderBy('id', 'asc');
        }]);
        
        $breakdown = $this->budgetService->getTaskWiseMonthlyBreakdown($project);
        $totalBudget = $project->tasks->sum('amount');
        
        return view('budgets.draft', [
            'project' => $project,
            'tasks' => $breakdown['tasks'],
            'months' => $breakdown['months'],
            'totalBudget' => $totalBudget,
        ]);
    }

    public function recalculate(Request $request, Project $project)
    {
        $project->load('tasks');
        $breakdown = $this->budgetService->getTaskWiseMonthlyBreakdown($project);
        $totalBudget = $project->tasks->sum('amount');
        
        return response()->json([
            'tasks' => $breakdown['tasks'],
            'months' => $breakdown['months'],
            'totalBudget' => $totalBudget,
        ]);
    }

    /**
     * Update actual cost for a task in a specific month
     */
    public function updateActualCost(Request $request, Task $task)
    {
        $validated = $request->validate([
            'month' => 'required|string',
            'actual_cost' => 'required|numeric|min:0',
            'earned_value_percentage' => 'required|numeric|min:0|max:100',
        ]);

        MonthlyActualCost::updateOrCreate(
            [
                'task_id' => $task->id,
                'month' => $validated['month'],
            ],
            [
                'actual_cost' => $validated['actual_cost'],
                'earned_value_percentage' => $validated['earned_value_percentage'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Actual cost updated successfully',
        ]);
    }
    /**
     * Show input page for a specific task
     */
    public function input(Task $task)
    {
        $start = \Carbon\Carbon::parse($task->start_date);
        $end = \Carbon\Carbon::parse($task->end_date);
        $months = [];

        // Generate months between start and end
        $period = \Carbon\CarbonPeriod::create($start, '1 month', $end);
        foreach ($period as $date) {
            $months[] = [
                'key' => $date->format('Y-m'),
                'label' => $date->format('M Y'),
                'year' => $date->format('Y')
            ];
        }

        $existingRecords = $task->monthlyActualCosts->keyBy('month');
        $plannedBudget = $this->budgetService->calculateMonthlyBudget($task);

        return view('budgets.input', compact('task', 'months', 'existingRecords', 'plannedBudget'));
    }

    /**
     * Store budget inputs for a task
     */
    public function storeInput(Request $request, Task $task)
    {
        $validated = $request->validate([
            'inputs' => 'required|array',
            'inputs.*.actual_cost' => 'nullable|numeric|min:0',
            'inputs.*.earned_value_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $plannedBudget = $this->budgetService->calculateMonthlyBudget($task);

        // Calculate total entered cost
        $totalEntered = collect($validated['inputs'])->sum('actual_cost');
        
        // Validate Total Budget
        if ($totalEntered > $task->amount + 0.01) { // 0.01 tolerance
            notify()->error("Total actual cost (" . number_format($totalEntered, 2) . ") exceeds task budget (" . number_format($task->amount, 2) . ")", 'Validation Error');
            return back()->withInput();
        }

        try {
            foreach ($validated['inputs'] as $month => $data) {
                // Only save if there's data or update existing
                if (isset($data['actual_cost']) || isset($data['earned_value_percentage'])) {
                    
                    $inputCost = $data['actual_cost'] ?? 0;

                    MonthlyActualCost::updateOrCreate(
                        [
                            'task_id' => $task->id,
                            'month' => $month,
                        ],
                        [
                            'actual_cost' => $inputCost,
                            'earned_value_percentage' => $data['earned_value_percentage'] ?? 0,
                        ]
                    );
                }
            }

            notify()->success('Budget inputs updated successfully', 'Success');
            return redirect()->route('projects.show', $task->project_id);
            
        } catch (\Throwable $th) {
            notify()->error('Failed to update inputs', 'Error');
            return back()->withInput();
        }
    }
}
