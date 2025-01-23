<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\BudgetEstimate;
use App\Models\BudgetCalculator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class BudgetCalculatorController extends Controller
{
    public function index($budgetEstimateID)
    {
        $budgetEstimate = BudgetEstimate::with('budgetCalculators')->findOrFail($budgetEstimateID);
        $budgetCalculators = $budgetEstimate->budgetCalculators()->orderBy('id', 'desc')->get();

        $totalCost = $budgetEstimate->budgetCalculators->sum('total');
        $totalTasks = $budgetEstimate->budgetCalculators->count();

        // Calculate budget metrics
        $plannedValue = $budgetEstimate->budget_amount;

        $earnedValue = 0;
        $actualCost = 0;
        $costVariance = 0;
        $scheduleVariance = 0;

        if ($totalTasks > 0) {
            $earnedValue = $this->calculateEarnedValue($budgetEstimate);
            $actualCost = $totalCost;
            $costVariance = $earnedValue - $actualCost;
            $scheduleVariance = $earnedValue - $plannedValue;
        }

        $bac = $budgetEstimate->budget_amount;

        return view('backend.budget_calculator.index', compact(
            'budgetEstimate',
            'budgetCalculators',
            'totalCost',
            'totalTasks',
            'plannedValue',
            'earnedValue',
            'actualCost',
            'costVariance',
            'scheduleVariance',
            'bac'
        ));
    }

    private function calculateEarnedValue($budgetEstimate)
    {
        $today = Carbon::now();
        $earnedValue = 0;
        $plannedValuePerTask = $budgetEstimate->budget_amount / $budgetEstimate->budgetCalculators->count();

        foreach ($budgetEstimate->budgetCalculators as $budgetCalculator) {

            if ($today >= $budgetCalculator->to_date) {

                $earnedValue += $plannedValuePerTask;
            } else if ($today >= $budgetCalculator->from_date) {

                $totalDays = $budgetCalculator->from_date->diffInDays($budgetCalculator->to_date);
                $completedDays = $budgetCalculator->from_date->diffInDays($today);

                $completion = min(100, ($completedDays / $totalDays) * 100);
                $earnedValue += ($plannedValuePerTask * $completion / 100);
            }
        }

        return $earnedValue;
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_name' => 'required|string|max:255',
            'from_date' => 'required',
            'to_date' => 'required'
        ]);

        try {

            $totalRate = $request->rate === 'fixed'
            ? $request->fixed_rate
            : $request->hourly_rate * $request->number_of_hours;

            BudgetCalculator::create([
                'budget_estimate_id' => $request->budget_estimate_id,
                'task_name' => $request->task_name,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date, // Fixed `to_date` to use the correct input field
                'rate' => $request->rate,
                'fixed_rate' => $request->rate === 'fixed' ? $request->fixed_rate : null,
                'hourly_rate' => $request->rate === 'hourly' ? $request->hourly_rate : null,
                'number_of_hours' => $request->rate === 'hourly' ? $request->number_of_hours : null,
                'total' => $totalRate,
            ]);

            notify()->success('Budget Calculator Created Successfully', 'Success');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            notify()->error('Budget Calculator Create Failed', 'Error');
        }

        return back();
    }
}
