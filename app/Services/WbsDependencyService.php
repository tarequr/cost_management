<?php
namespace App\Services;

use App\Models\Task;
use App\Models\TaskDependency;

class WbsDependencyService
{
    public function propagateDates(Task $task)
    {
        // 1. Calculate dates for THIS task based on its dependency (if any)
        $dependency = $task->dependency;
        
        if ($dependency) {
            $predecessor = Task::find($dependency->depends_on_task_id); // Depends on this task
            if ($predecessor) {
                // Duration in months (approximate, we can refine logic later)
                // If duration is missing, calculate it from existing dates or default to 1
                $duration = $task->duration ?: 1;

                if ($dependency->type === 'FF') {
                    // Finish to Finish: End date same as predecessor
                    $task->end_date = $predecessor->end_date;
                    // Start date = End date - Duration
                    // Note: Carbon subMonths subtracts from the date. 
                    // E.g., End: Dec 2026. Duration 1. Start should be Dec 2026? 
                    // Or if End is 31st Dec. Start is 1st Dec?
                    // User logic: Start = End - Duration.
                    // Let's ensure dates are instances
                    $endDate = $task->end_date; 
                    $task->start_date = $endDate->copy()->subMonths($duration - 1); // -1 because inclusive?
                    // Example: Duration 1 month. Jan 1 to Jan 31.
                    // End = Jan 31. Start = Jan 31 - 0 months = Jan 31? No.
                    // Let's stick to simple "Add/Sub months" logic for now.
                    // If End is 2026-02-01 (Feb). Duration 1.
                    // Start = 2026-02-01. 
                    
                    // Better logic based on User's Excel:
                    // 1.1 Electrical: 12 months. Jan-Dec.
                    // 1.2 Cooling (FF): 12 months. Jan-Dec.
                    // So if Predecessor End = Dec. Successor End = Dec.
                    // Successor Start = Dec - 12 months + 1?
                    
                    $task->start_date = $task->end_date->copy()->subMonths($duration)->addMonth()->startOfMonth();
                     // Re-align end date to end of month just in case
                    $task->end_date = $task->end_date->endOfMonth();
                    
                } elseif ($dependency->type === 'SS') {
                    // Start to Start: Start date same as predecessor
                    $task->start_date = $predecessor->start_date->startOfMonth();
                    // End date = Start + Duration
                    $task->end_date = $task->start_date->copy()->addMonths($duration)->subDay()->endOfMonth();
                }
                $task->save();
            }
        }

        // 2. Find all tasks that depend on THIS task (Successors) and update them
        // We need to look for TaskDependencies where 'depends_on_task_id' == $task->id
        $successors = TaskDependency::where('depends_on_task_id', $task->id)->get();
        foreach ($successors as $successorDep) {
            $successorTask = Task::find($successorDep->task_id);
            if ($successorTask) {
                $this->propagateDates($successorTask);
            }
        }
    }

    public function validateNoCircularDependency(Task $task, $dependsOnId)
    {
        // Simple check for circular dependency
        if ($task->id === $dependsOnId) {
            return false;
        }

        $visited = [$task->id];
        $current = $dependsOnId;
        while ($current) {
            $dep = TaskDependency::where('task_id', $current)->first();
            if (! $dep) {
                break;
            }

            if (in_array($dep->depends_on_task_id, $visited)) {
                return false;
            }

            $visited[] = $dep->depends_on_task_id;
            $current   = $dep->depends_on_task_id;
        }
        return true;
    }
}
