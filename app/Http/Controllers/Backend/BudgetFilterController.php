<?php
namespace App\Http\Controllers\Backend;

use App\Helpers\BudgetHelper;
use App\Http\Controllers\Controller;
use App\Models\BudgetCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BudgetFilterController extends Controller
{
    public function filter(Request $request)
    {
        $request->validate([
            'from_month'         => ['required', 'date_format:Y-m'],
            'to_month'           => ['required', 'date_format:Y-m', 'after_or_equal:from_month'],
            'expected_amount'    => ['required', 'numeric'],
            'budget_estimate_id' => ['required'],
        ]);

        $startRange = Carbon::createFromFormat('Y-m', $request->from_month)->startOfMonth(); // e.g. 2025‑05‑01 00:00:00
        $endRange   = Carbon::createFromFormat('Y-m', $request->to_month)->endOfMonth();     // e.g. 2025‑08‑31 23:59:59

        $tasks = BudgetCalculator::where('budget_estimate_id', $request->budget_estimate_id)
            ->whereDate('from_date', '<=', $endRange) // task starts before the span ends
            ->whereDate('to_date', '>=', $startRange) // task ends after the span starts
            ->get();

        $monthlyAmounts = [];
        for ($cursor = $startRange->copy(); $cursor <= $endRange; $cursor->addMonth()) {
            $monthlyAmounts[$cursor->format('Y-m')] = 0;
        }

        foreach ($tasks as $task) {
            for ($cursor = $startRange->copy(); $cursor <= $endRange; $cursor->addMonth()) {

                $amount = BudgetHelper::calculateMonthlyAmount(
                    $task->from_date,
                    $task->to_date,
                    $task->fixed_rate,
                    $cursor->month, // 1‑12
                    $cursor->year   // 2025, 2026, …
                );

                $monthlyAmounts[$cursor->format('Y-m')] += $amount;
            }
        }

        $totalAmount = array_sum($monthlyAmounts);

        dd($totalAmount, $monthlyAmounts);
        return view('backend.budget_calculator.filter_data', [
            'budgetCalculators' => $tasks,
            'monthlyAmounts'    => $monthlyAmounts,
            'totalAmount'       => $totalAmount,
            // 'fromMonth'         => $fromMonth,
            // 'toMonth'           => $toMonth,
        ]);
    }
}
