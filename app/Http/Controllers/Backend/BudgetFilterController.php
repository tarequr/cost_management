<?php
namespace App\Http\Controllers\Backend;

use App\Helpers\BudgetHelper;
use App\Http\Controllers\Controller;
use App\Models\BudgetCalculator;
use App\Models\BudgetEstimate;
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

        $budgetEstimate = BudgetEstimate::findOrFail($request->budget_estimate_id);

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

        //Another section
        $totalTasks = BudgetCalculator::where('budget_estimate_id', $request->budget_estimate_id)->get();

        if ($totalTasks->isEmpty()) {
            notify()->error('No tasks found for the selected budget estimate.', 'Error');
            return back();
        }

        $projectStart = Carbon::parse($totalTasks->min('from_date'))->startOfMonth();
        $projectEnd   = Carbon::parse($totalTasks->max('to_date'))->startOfMonth();

        $selectedMonths = $startRange->diffInMonths($endRange) + 1; //input data

        $totalProjectMonths = $projectStart->diffInMonths($projectEnd) + 1; //database data

        $totalRate = $totalTasks->sum('fixed_rate');

        $ac = 0; // Actual Cost

        foreach ($tasks as $task) {
            $ac += BudgetHelper::calculateMonthlyAmount(
                $task->from_date,
                $task->to_date,
                $task->fixed_rate,
                $startRange->month, // August
                $startRange->year   // 2025
            );
        }

        $percentage = 0;
        if ($totalProjectMonths > 0) {
            $percentage = ($selectedMonths / $totalProjectMonths) * 100;
        }

        $percentageRate = $totalRate * ($percentage / 100);

        $bac = $totalRate;                      // Budget at Completion
        $pv  = (int) $request->expected_amount; // Planned Value
        $ac  = array_sum($monthlyAmounts);      // $ac  = $tasks->sum('fixed_rate');       // Actual Cost
        $ev  = $percentageRate;                 // Earned Value
        $cv  = $ev - $ac;                       // Cost Variance
        $sv  = $ev - $pv;                       // Schedule Variance

        $labels   = []; // ["May 2025", "Jun 2025", …]
        $acSeries = []; // cumulative AC
        $pvSeries = []; // cumulative PV
        $evSeries = []; // cumulative EV

        $cumAc      = $cumPv      = $cumEv      = 0;
        $pvPerMonth = $pv / max(1, $selectedMonths);
        $evPerMonth = $ev / max(1, $selectedMonths);

        foreach ($monthlyAmounts as $ym => $acForThisMonth) {
            $labels[] = Carbon::createFromFormat('Y-m', $ym)->format('M Y');

            $cumAc += $acForThisMonth;
            // $acSeries[] = round($cumAc, 2);
            $acSeries[] = round($cumAc, );

            $cumPv += $pvPerMonth;
            $pvSeries[] = round($cumPv, 2);

            $cumEv += $evPerMonth;
            $evSeries[] = round($cumEv, 2);
        }

        // dd($labels, $acSeries, $pvSeries, $evSeries);

        // dd("BAC: $bac, PV: $pv, AC: $ac, EV: $ev, CV: $cv, SV: $sv");

        return view('backend.budget_calculator.filter_data', [
            'budgetEstimate'    => $budgetEstimate,
            'budgetCalculators' => $tasks,
            'monthlyAmounts'    => $monthlyAmounts,
            'totalAmount'       => $totalAmount,
            'fromMonth'         => $startRange->format('F Y'),
            'toMonth'           => $endRange->format('F Y'),
            'totalTasks'        => $totalTasks,
            'bac'               => $bac,
            'pv'                => $pv,
            'ac'                => $ac,
            'ev'                => $ev,
            'cv'                => $cv,
            'sv'                => $sv,

            'chartLabels'       => $labels,
            'chartAC'           => $acSeries,
            'chartPV'           => $pvSeries,
            'chartEV'           => $evSeries,
        ]);
    }
}
