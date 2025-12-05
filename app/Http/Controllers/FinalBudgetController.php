<?php
namespace App\Http\Controllers;

use App\Models\Project;

class FinalBudgetController extends Controller
{
    public function show(Project $project)
    {
        $tasks = $project->tasks;
        // For demo, assume actualCosts is same as tasks (replace with real cost tracking)
        $actualCosts = $tasks;
        $evmService  = new \App\Services\EvmCalculationService();
        $evm         = $evmService->calculate($tasks, $actualCosts);
        // Ensure all required fields are present, even if zero
        $fields = ['PV', 'AC', 'EV', 'SPI', 'CPI', 'CV', 'SV', 'BAC', 'ETC', 'EAC'];
        $data   = [];
        foreach ($fields as $field) {
            $data[$field] = $evm[strtolower($field)] ?? 0;
        }
        \App\Models\FinalBudget::updateOrCreate(['project_id' => $project->id], $data);
        return view('budgets.final', compact('project', 'evm'));
    }
}
