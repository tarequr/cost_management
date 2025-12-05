<?php
namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        try {
            $project = Project::create(['name' => $validated['name']]);
            foreach ($validated['tasks'] as $taskData) {
                $taskData['start_date'] .= '-01';
                $taskData['end_date'] .= '-01';
                $project->tasks()->create($taskData);
            }
            notify()->success('Project created successfully', 'Success');
            return redirect()->route('projects.show', $project);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            notify()->error('Project creation failed', 'Error');
            return back();
        }
    }

    public function show(Project $project)
    {
        $project->load('tasks');
        return view('projects.show', compact('project'));
    }
}
