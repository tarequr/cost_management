<?php
namespace App\Services;

use App\Models\Task;
use App\Models\TaskDependency;
use Carbon\Carbon;

class WbsDependencyService
{
    /**
     * Propagate dates based on precedence relationships
     * 
     * Precedence Types:
     * - F-F (Finish-to-Finish): Successor finishes when predecessor finishes
     * - S-S (Start-to-Start): Successor starts when predecessor starts
     * - F-S (Finish-to-Start): Successor starts when predecessor finishes (most common)
     * - S-F (Start-to-Finish): Successor finishes when predecessor starts (rare)
     */
    public function propagateDates(Task $task)
    {
        // 1. Calculate dates for THIS task based on its dependency (if any)
        $dependency = $task->dependency;
        
        if ($dependency) {
            $predecessor = Task::find($dependency->depends_on_task_id);
            
            if ($predecessor && $task->duration) {
                $duration = $task->duration;
                
                switch ($dependency->type) {
                    case 'FF': // Finish-to-Finish
                        // Successor must finish when predecessor finishes
                        // End date = Predecessor's end date
                        // Start date = End date - Duration
                        $task->end_date = Carbon::parse($predecessor->end_date)->endOfMonth();
                        $task->start_date = $task->end_date->copy()
                            ->subMonths($duration - 1)
                            ->startOfMonth();
                        break;
                        
                    case 'SS': // Start-to-Start
                        // Successor must start when predecessor starts
                        // Start date = Predecessor's start date
                        // End date = Start date + Duration
                        $task->start_date = Carbon::parse($predecessor->start_date)->startOfMonth();
                        $task->end_date = $task->start_date->copy()
                            ->addMonths($duration - 1)
                            ->endOfMonth();
                        break;
                        
                    case 'FS': // Finish-to-Start (most common)
                        // Successor starts after predecessor finishes
                        // Start date = Predecessor's end date + 1 day
                        // End date = Start date + Duration
                        $task->start_date = Carbon::parse($predecessor->end_date)
                            ->addDay()
                            ->startOfMonth();
                        $task->end_date = $task->start_date->copy()
                            ->addMonths($duration - 1)
                            ->endOfMonth();
                        break;
                        
                    case 'SF': // Start-to-Finish (rare)
                        // Successor finishes when predecessor starts
                        // End date = Predecessor's start date
                        // Start date = End date - Duration
                        $task->end_date = Carbon::parse($predecessor->start_date)->endOfMonth();
                        $task->start_date = $task->end_date->copy()
                            ->subMonths($duration - 1)
                            ->startOfMonth();
                        break;
                        
                    default:
                        // No change to dates
                        break;
                }
                
                $task->save();
            }
        }

        // 2. Find all tasks that depend on THIS task (Successors) and update them recursively
        $successors = TaskDependency::where('depends_on_task_id', $task->id)->get();
        
        foreach ($successors as $successorDep) {
            $successorTask = Task::find($successorDep->task_id);
            if ($successorTask) {
                // Recursive call to propagate changes down the dependency chain
                $this->propagateDates($successorTask);
            }
        }
    }

    /**
     * Validate that adding this dependency won't create a circular reference
     */
    public function validateNoCircularDependency(Task $task, $dependsOnId): bool
    {
        // Can't depend on itself
        if ($task->id === $dependsOnId) {
            return false;
        }

        // Check for circular dependencies using depth-first search
        $visited = [$task->id];
        $current = $dependsOnId;
        
        while ($current) {
            // Find if current task depends on another task
            $dep = TaskDependency::where('task_id', $current)->first();
            
            if (!$dep) {
                // No more dependencies in chain
                break;
            }

            // Check if we've already visited this dependency (circular!)
            if (in_array($dep->depends_on_task_id, $visited)) {
                return false; // Circular dependency detected
            }

            $visited[] = $dep->depends_on_task_id;
            $current = $dep->depends_on_task_id;
        }
        
        return true; // No circular dependency
    }

    /**
     * Calculate Critical Path (CPM)
     * Returns array of critical tasks
     */
    public function calculateCriticalPath($projectId): array
    {
        // This can be implemented later for CPM analysis
        // For now, return empty array
        return [];
    }

    /**
     * Get all tasks in topological order (dependency order)
     */
    public function getTopologicalOrder($projectId): array
    {
        $tasks = Task::where('project_id', $projectId)->get();
        $sorted = [];
        $visited = [];
        
        foreach ($tasks as $task) {
            if (!in_array($task->id, $visited)) {
                $this->topologicalSort($task, $visited, $sorted);
            }
        }
        
        return array_reverse($sorted);
    }
    
    private function topologicalSort($task, &$visited, &$sorted)
    {
        $visited[] = $task->id;
        
        // Visit all dependencies first
        if ($task->dependency) {
            $predecessor = Task::find($task->dependency->depends_on_task_id);
            if ($predecessor && !in_array($predecessor->id, $visited)) {
                $this->topologicalSort($predecessor, $visited, $sorted);
            }
        }
        
        $sorted[] = $task;
    }
}
