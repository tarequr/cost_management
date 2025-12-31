<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class MonthlyBudgetService
{
    /**
     * Calculate monthly budget breakdown for a task
     * Divides task amount by duration months
     */
    public function calculateMonthlyBudget(Task $task): array
    {
        $monthlyBudget = [];
        
        if (!$task->duration || $task->duration <= 0) {
            return $monthlyBudget;
        }

        $monthlyCost = round($task->cost / $task->duration, 2);
        
        // Create period from start_date to end_date
        $period = CarbonPeriod::create($task->start_date, '1 month', $task->end_date);
        
        foreach ($period as $date) {
            $monthKey = $date->format('Y-m');
            $monthlyBudget[$monthKey] = $monthlyCost;
        }

        return $monthlyBudget;
    }

    /**
     * Get all unique months covered by project tasks
     */
    public function getProjectMonths(Project $project): array
    {
        $tasks = $project->tasks;
        
        if ($tasks->isEmpty()) {
            return [];
        }

        $minDate = $tasks->min('start_date');
        $maxDate = $tasks->max('end_date');

        if (!$minDate || !$maxDate) {
            return [];
        }

        $months = [];
        $period = CarbonPeriod::create(
            Carbon::parse($minDate)->startOfMonth(),
            '1 month',
            Carbon::parse($maxDate)->endOfMonth()
        );

        foreach ($period as $date) {
            $months[] = [
                'key' => $date->format('Y-m'),
                'label' => $date->format('M Y'),
                'year' => $date->format('Y'),
            ];
        }

        return $months;
    }

    /**
     * Get project-wide monthly budget aggregated from all tasks
     */
    public function getProjectMonthlyBudget(Project $project): array
    {
        $projectBudget = [];
        
        foreach ($project->tasks as $task) {
            $taskBudget = $this->calculateMonthlyBudget($task);
            
            foreach ($taskBudget as $month => $cost) {
                if (!isset($projectBudget[$month])) {
                    $projectBudget[$month] = 0;
                }
                $projectBudget[$month] += $cost;
            }
        }

        return $projectBudget;
    }

    /**
     * Get task-wise monthly breakdown with task details
     */
    public function getTaskWiseMonthlyBreakdown(Project $project): array
    {
        $breakdown = [];
        $months = $this->getProjectMonths($project);
        
        foreach ($project->tasks as $task) {
            $taskMonthly = $this->calculateMonthlyBudget($task);
            
            $breakdown[] = [
                'task' => $task,
                'monthly_budget' => $taskMonthly,
                'total' => $task->cost,
            ];
        }

        return [
            'tasks' => $breakdown,
            'months' => $months,
        ];
    }
}
