<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\FinalBudget;
use App\Services\EVMCalculationService;
use Carbon\Carbon;

class FinalBudgetController extends Controller
{
    protected EVMCalculationService $evmService;

    public function __construct(EVMCalculationService $evmService)
    {
        $this->evmService = $evmService;
    }

    public function show(Project $project)
    {
        $project->load('tasks.monthlyActualCosts');
        
        // Calculate EVM metrics up to current month
        $currentMonth = Carbon::now()->format('Y-m');
        $evmMetrics = $this->evmService->calculateEVM($project, $currentMonth);
        
        // Get monthly breakdown data
        $monthlyData = $this->evmService->getMonthlyEVMData($project);
        
        // Get performance status for indicators
        $spiStatus = $this->evmService->getPerformanceStatus($evmMetrics['SPI']);
        $cpiStatus = $this->evmService->getPerformanceStatus($evmMetrics['CPI']);
        
        // Save to final_budgets table
        FinalBudget::updateOrCreate(
            ['project_id' => $project->id],
            [
                'PV' => $evmMetrics['PV'],
                'AC' => $evmMetrics['AC'],
                'EV' => $evmMetrics['EV'],
                'SPI' => $evmMetrics['SPI'],
                'CPI' => $evmMetrics['CPI'],
                'CV' => $evmMetrics['CV'],
                'SV' => $evmMetrics['SV'],
                'BAC' => $evmMetrics['BAC'],
                'ETC' => $evmMetrics['ETC'],
                'EAC' => $evmMetrics['EAC'],
            ]
        );
        
        return view('budgets.final', [
            'project' => $project,
            'evm' => $evmMetrics,
            'monthlyData' => $monthlyData,
            'spiStatus' => $spiStatus,
            'cpiStatus' => $cpiStatus,
        ]);
    }
}
