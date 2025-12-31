<?php
namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('id', 'asc')->get();
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                     => 'required|string|max:255',
            'tasks'                    => 'required|array',
            'tasks.*.task_name'        => 'required|string|max:255',
            'tasks.*.start_date'       => 'required|date',
            'tasks.*.duration'         => 'required|integer|min:1',
            'tasks.*.cost'             => 'required|numeric|min:0',
            'tasks.*.has_precedence'   => 'required|in:yes,no',
            'tasks.*.depends_on'       => 'required_if:tasks.*.has_precedence,yes|nullable',
            'tasks.*.precedence_type'  => 'required_if:tasks.*.has_precedence,yes|in:FF,SS|nullable',
        ]);

        try {
            DB::beginTransaction();
            $project = Project::create(['name' => $validated['name']]);
            $createdTasks = [];

            foreach ($validated['tasks'] as $index => $taskData) {
                $duration = (int)$taskData['duration'];
                $startDateStr = '';

                if ($taskData['has_precedence'] === 'yes' && isset($taskData['depends_on']) && isset($createdTasks[$taskData['depends_on']])) {
                    $parentTask = $createdTasks[$taskData['depends_on']];
                    $parentStart = \Carbon\Carbon::parse($parentTask->start_date);
                    $parentEnd = \Carbon\Carbon::parse($parentTask->end_date);

                    if ($taskData['precedence_type'] === 'SS') {
                        $start = $parentStart->copy();
                    } else { // FF
                        $start = $parentEnd->copy()->subMonths($duration - 1);
                    }
                } else {
                    $start = \Carbon\Carbon::parse($taskData['start_date'] . '-01');
                }

                $end = $start->copy()->addMonths($duration - 1);
                
                $task = $project->tasks()->create([
                    'task_name'  => $taskData['task_name'],
                    'start_date' => $start->toDateString(),
                    'end_date'   => $end->toDateString(),
                    'cost'       => $taskData['cost'],
                    'duration'   => $duration,
                ]);
                
                $createdTasks[$index] = $task;
            }

            // Create dependencies
            foreach ($validated['tasks'] as $index => $taskData) {
                if ($taskData['has_precedence'] === 'yes' && isset($taskData['depends_on'])) {
                    $dependsOnIndex = $taskData['depends_on'];
                    if (isset($createdTasks[$dependsOnIndex])) {
                        $createdTasks[$index]->dependency()->create([
                            'depends_on_task_id' => $createdTasks[$dependsOnIndex]->id,
                            'type'               => $taskData['precedence_type'],
                        ]);
                    }
                }
            }

            DB::commit();
            notify()->success('Project created successfully', 'Success');
            return redirect()->route('projects.show', $project);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            notify()->error('Project creation failed: ' . $th->getMessage(), 'Error');
            return back();
        }
    }

    public function show(Project $project)
    {
        $project->load(['tasks' => function ($query) {
            $query->orderBy('id', 'asc');
        }]);
        
        return view('projects.show', [
            'project' => $project,
            'allTasksFinalized' => $project->allTasksFinalized(),
        ]);
    }
}
