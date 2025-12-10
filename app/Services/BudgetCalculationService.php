<?php
namespace App\Services;

class BudgetCalculationService
{
    public function calculateMonthlyBudget($tasks)
    {
        $monthly = [];
        foreach ($tasks as $task) {
            $start          = \Carbon\Carbon::parse($task->start_date);
            $end            = \Carbon\Carbon::parse($task->end_date);
            $months         = $start->diffInMonths($end) + 1;
            $amountPerMonth = $task->amount / $months;
            for ($i = 0; $i < $months; $i++) {
                $monthKey = $start->copy()->addMonths($i)->format('Y-m');
                if (! isset($monthly[$monthKey])) {
                    $monthly[$monthKey] = 0;
                }

                $monthly[$monthKey] += $amountPerMonth;
            }
        }
        return $monthly;
    }
}
