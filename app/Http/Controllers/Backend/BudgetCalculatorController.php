<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BudgetEstimate;
use Illuminate\Http\Request;

class BudgetCalculatorController extends Controller
{
    public function index($budgetEstimateID)
    {
        $budgetEstimate = BudgetEstimate::findOrFail($budgetEstimateID);
        return view('backend.budget_calculator.index', compact('budgetEstimate'));
    }

    public function store(Request $request)
    {
        dd($request->all());

        //validation
        //make migration
            // "task_name" => "dem1"
            // "from_date" => "2025-01-11"
            // "to_date" => "2025-01-13"
            // "rate" => "fixed"
            // "fixed_rate" => "80"
            // "hourly_rate" => null
            // "number_of_hours" => null
            // "total" => "240"
            // "budget_estimate_id" => "1"
            // 'user_id' => auth()->id()
        //make model
        //business logic

    }
}
