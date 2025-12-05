<?php
namespace App\Services;

use App\Models\Task;
use App\Models\TaskDependency;

class WbsDependencyService
{
    public function propagateDates(Task $task)
    {
        // Get dependencies
        $dependencies = $task->dependencies;
        foreach ($dependencies as $dependency) {
            $depTask = Task::find($dependency->depends_on_task_id);
            if ($depTask) {
                if ($dependency->type === 'FF') {
                    $task->end_date = $depTask->end_date;
                } elseif ($dependency->type === 'SS') {
                    $task->start_date = $depTask->start_date;
                }
                $task->save();
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
