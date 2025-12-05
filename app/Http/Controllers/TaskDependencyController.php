<?php
namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskDependencyController extends Controller
{
    public function index(Task $task)
    {
        $tasks        = Task::where('project_id', $task->project_id)->where('id', '!=', $task->id)->get();
        $dependency   = $task->dependency;
        return view('tasks.dependencies', compact('task', 'tasks', 'dependency'));
    }

    public function store(Request $request, Task $task)
    {
        $request->validate([
            'depends_on_task_id' => 'required|exists:tasks,id',
            'type'               => 'required|in:FF,SS',
        ]);
        $wbsService = app(\App\Services\WbsDependencyService::class);
        try {
            // Validate no circular dependency
            if (! $wbsService->validateNoCircularDependency($task, $request->depends_on_task_id)) {
                notify()->error('Circular dependency detected!', 'Error');
                return back();
            }
            \App\Models\TaskDependency::updateOrCreate(
                ['task_id' => $task->id],
                ['depends_on_task_id' => $request->depends_on_task_id, 'type' => $request->type]
            );
            $wbsService->propagateDates($task);
            notify()->success('Dependency saved!', 'Success');
            return redirect()->route('projects.show', $task->project_id);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            notify()->error('Something went wrong!', 'Error');
            return back();
        }
    }
}
