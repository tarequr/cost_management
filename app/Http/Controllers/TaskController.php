<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        try {
            foreach ($validated['tasks'] as $taskData) {
                $taskData['start_date'] .= '-01';
                $taskData['end_date'] .= '-01';
                $project->tasks()->create($taskData);
            }
            notify()->success('Tasks added successfully', 'Success');
            return redirect()->route('projects.show', $project);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            notify()->error('Failed to add tasks', 'Error');
            return back();
        }
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'task_name'  => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'amount'     => 'required|numeric|min:0',
        ]);
        try {
            $validated['start_date'] .= '-01';
            $validated['end_date'] .= '-01';
            $task->update($validated);
            notify()->success('Task updated successfully', 'Success');
            return back();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            notify()->error('Task update failed', 'Error');
            return back();
        }
    }
}
