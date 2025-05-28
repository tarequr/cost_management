<?php
namespace App\Helpers;

use Carbon\Carbon;

class BudgetHelper
{
    public static function calculateMonthlyAmount($fromDate, $toDate, $totalAmount, $filterMonth, $filterYear)
    {
        $start = Carbon::parse($fromDate);
        $end   = Carbon::parse($toDate);

                                                   // Calculate total days in the task period
        $totalDays = $start->diffInDays($end) + 1; // +1 to include both start and end dates

        // Calculate daily rate
        $dailyRate = $totalAmount / $totalDays;

        // Initialize monthly amount
        $monthlyAmount = 0;

        // Loop through each day in the task period
        $currentDay = $start->copy();
        while ($currentDay <= $end) {
            // Check if this day falls in the filtered month/year
            if ($currentDay->month == $filterMonth && $currentDay->year == $filterYear) {
                $monthlyAmount += $dailyRate;
            }
            $currentDay->addDay();
        }

        return round($monthlyAmount, 2);
    }
}
