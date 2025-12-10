<?php
namespace App\Services;

class EvmCalculationService
{
    public function calculate($tasks, $actualCosts)
    {
        $bac = $tasks->sum('amount');
        $ev  = 0;
        $pv  = 0;
        foreach ($tasks as $task) {
            $ev += $task->amount * ($task->progress ?? 0); // progress: 0-1
            $pv += $task->amount * ($task->planned_progress ?? 0);
        }
        $ac  = $actualCosts->sum('amount');
        $spi = $pv ? $ev / $pv : 0;
        $cpi = $ac ? $ev / $ac : 0;
        $cv  = $ev - $ac;
        $sv  = $ev - $pv;
        $etc = $bac - $ev;
        $eac = $cpi ? $bac / $cpi : 0;
        return compact('pv', 'ac', 'ev', 'spi', 'cpi', 'cv', 'sv', 'bac', 'etc', 'eac');
    }
}
