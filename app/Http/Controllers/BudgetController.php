<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\BudgetCalculationService;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function draft(Project $project)
    {
        $tasks         = $project->tasks;
        $budgetService = new BudgetCalculationService();
        $monthly       = $budgetService->calculateMonthlyBudget($tasks);
        $totalAmount   = $tasks->sum('amount');
        $totalDuration = count($monthly);
        return view('budgets.draft', compact('project', 'monthly', 'totalAmount', 'totalDuration'));
    }

    // AJAX endpoint for recalculation
    public function recalculate(Request $request, Project $project)
    {
        $tasks         = $project->tasks;
        $budgetService = new BudgetCalculationService();
        $monthly       = $budgetService->calculateMonthlyBudget($tasks);
        $totalAmount   = $tasks->sum('amount');
        $totalDuration = count($monthly);
        return response()->json([
            'monthly'       => $monthly,
            'totalAmount'   => $totalAmount,
            'totalDuration' => $totalDuration,
        ]);
    }
}
