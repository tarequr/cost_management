<?php
namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'tasks'              => 'required|array',
            'tasks.*.task_name'  => 'required|string|max:255',
            'tasks.*.start_date' => 'required|date',
            'tasks.*.end_date'   => 'required|date|after_or_equal:tasks.*.start_date',
            'tasks.*.amount'     => 'required|numeric|min:0',
        ]);
        $project = Project::create(['name' => $validated['name']]);
        foreach ($validated['tasks'] as $taskData) {
            $project->tasks()->create($taskData);
        }
        return redirect()->route('projects.show', $project);
    }

    public function show(Project $project)
    {
        $project->load('tasks');
        return view('projects.show', compact('project'));
    }
}
