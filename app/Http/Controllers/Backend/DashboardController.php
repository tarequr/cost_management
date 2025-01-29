<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\BudgetEstimate;
use App\Models\BudgetCalculator;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     return view('backend.dashboard.index');
    // }


    public function index()
    {
        $chartData = [
            'budgetOverview' => $this->getBudgetOverviewData(),
            'taskProgress' => $this->getTaskProgressData(),
            'costVariance' => $this->getCostVarianceData()
        ];

        return view('backend.dashboard.view', compact('chartData'));
    }

    private function getBudgetOverviewData()
    {
        $projects = BudgetEstimate::where('user_id', auth()->id())
            ->latest()
            // ->take(5)
            ->get();

        $data = [
            'projects' => [],
            'planned' => [],
            'actual' => []
        ];

        foreach ($projects as $project) {
            $data['projects'][] = $project->project_name;
            $data['planned'][] = $project->budget_amount;
            $data['actual'][] = $project->budgetCalculators->sum('total');
        }

        return $data;
    }

    private function getTaskProgressData()
    {
        $allTasks = BudgetCalculator::whereHas('budgetEstimate', function($query) {
            $query->where('user_id', auth()->id());
        })->get();

        $completed = 0;
        $inProgress = 0;
        $notStarted = 0;
        $today = Carbon::now();

        foreach ($allTasks as $task) {
            $startDate = Carbon::parse($task->from_date);
            $endDate = Carbon::parse($task->to_date);

            if ($today > $endDate) {
                $completed++;
            } elseif ($today >= $startDate && $today <= $endDate) {
                $inProgress++;
            }
            // else {
            //     $notStarted++;
            // }
        }

        $notStarted = BudgetEstimate::with('budgetCalculators')
            ->where('user_id', auth()->id())
            ->whereDoesntHave('budgetCalculators')
            ->count();

            // dd($notStarted);

        return [
            'labels' => ['Completed', 'In Progress', 'Not Started'],
            'values' => [$completed, $inProgress, $notStarted]
        ];
    }

    private function getCostVarianceData()
    {
        $projects = BudgetEstimate::where('user_id', auth()->id())
            ->latest()
            ->take(10)
            ->get();

        $data = [
            'dates' => [],
            'values' => []
        ];

        foreach ($projects as $project) {
            $data['dates'][] = $project->created_at->format('M d');

            $plannedValue = $project->budget_amount;
            $actualCost = $project->budgetCalculators->sum('total');
            $data['values'][] = $plannedValue - $actualCost;
        }

        return $data;
    }
}
