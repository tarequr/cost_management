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

        // dd($totalAmount, $monthlyAmounts);

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
        // dd($startRange);

        // dd($endRange, $projectStart);
        // dd($endRange < $projectStart);
        // // Check for overlap
        // if ($endRange < $projectStart || $startRange > $projectEnd) {
        //     dd('No overlap');
        //     $message = 'Your selected time range does not match the project\'s time range. The project ran from ' .
        //     $projectStart->format('F Y') . ' to ' . $projectEnd->format('F Y') . '.';

        //     notify()->error($message, 'Error');
        //     return back();
        // }

        $percentage = 0;
        if ($totalProjectMonths > 0) {
            $percentage = ($selectedMonths / $totalProjectMonths) * 100;
        }

        // dd($percentage);

        $percentageRate = $totalRate * ($percentage / 100);

        // dd($percentageRate);

        $bac = $totalRate;                      // Budget at Completion
        $pv  = (int) $request->expected_amount; // Planned Value
        $ac  = $tasks->sum('fixed_rate');       // Actual Cost
        $ev  = $percentageRate;                 // Earned Value
        $cv  = $ev - $ac;                       // Cost Variance
        $sv  = $ev - $pv;                       // Schedule Variance

        // dd($bac, 'PV:' . $pv, $ac, 'EV: ' . $ev, $cv, $sv);

        return view('backend.budget_calculator.filter_data', [
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
        ]);
    }
}
