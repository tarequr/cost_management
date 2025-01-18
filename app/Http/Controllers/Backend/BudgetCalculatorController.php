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
        //
    }
}
