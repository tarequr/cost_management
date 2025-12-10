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
        $project->load('tasks');
        
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
}
