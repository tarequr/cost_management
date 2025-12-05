<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'tasks'              => 'required|array',
            'tasks.*.task_name'  => 'required|string|max:255',
            'tasks.*.start_date' => 'required|date',
            'tasks.*.end_date'   => 'required|date|after_or_equal:tasks.*.start_date',
            'tasks.*.amount'     => 'required|numeric|min:0',
        ]);
        foreach ($validated['tasks'] as $taskData) {
            $project->tasks()->create($taskData);
        }
        return redirect()->route('projects.show', $project);
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'task_name'  => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'amount'     => 'required|numeric|min:0',
        ]);
        $task->update($validated);
        return back();
    }
}
