<?php
namespace App\Http\Controllers\Backend;

use App\Helpers\BudgetHelper;
use App\Http\Controllers\Controller;
use App\Models\BudgetCalculator;
use Illuminate\Http\Request;

class BudgetFilterController extends Controller
{
    public function filter(Request $request)
    {
        // return $request;

        $request->validate([
            'from_month'         => 'required|numeric|between:1,12',
            'to_month'           => 'nullable|numeric|between:1,12|gte:from_month',
            'expected_amount'    => 'required|numeric',
            'budget_estimate_id' => 'required',
        ]);

        // dd($request->all());

                                // $budgetEstimate = BudgetEstimate::findOrFail($request->budget_estimate_id);
        $year      = date('Y'); // or make this selectable
        $fromMonth = $request->from_month;
        $toMonth   = $request->to_month ?: $fromMonth;

        $tasks = BudgetCalculator::where('budget_estimate_id', $request->budget_estimate_id)
            ->where(function ($query) use ($year, $fromMonth, $toMonth) {
                $query->where(function ($subQuery) use ($year, $fromMonth, $toMonth) {
                    // Tasks that overlap with the selected month range
                    $subQuery->whereYear('from_date', $year)
                        ->whereMonth('from_date', '<=', $toMonth)
                        ->whereMonth('to_date', '>=', $fromMonth);
                });
            })->get();

        // dd($tasks);
        // dd('Waiting for calculation');

        $monthlyAmounts = [];
        $totalAmount    = 0;

        foreach ($tasks as $task) {
            // Calculate for each month in the selected range
            for ($month = $fromMonth; $month <= $toMonth; $month++) {
                $amount = BudgetHelper::calculateMonthlyAmount(
                    $task->from_date,
                    $task->to_date,
                    $task->fixed_rate,
                    $month,
                    $year
                );

                if (! isset($monthlyAmounts[$month])) {
                    $monthlyAmounts[$month] = 0;
                }
                $monthlyAmounts[$month] += $amount;
            }
        }

        $totalAmount = array_sum($monthlyAmounts);

        // dd($totalAmount, $monthlyAmounts);
        // dd('Waiting for calculation');

        return view('backend.budget_calculator.filter_data', [
            'budgetCalculators' => $tasks,
            'monthlyAmounts'    => $monthlyAmounts,
            'totalAmount'       => $totalAmount,
            'fromMonth'         => $fromMonth,
            'toMonth'           => $toMonth,
        ]);
    }
}
