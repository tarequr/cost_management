<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyActualCost extends Model
{
    protected $fillable = [
        'task_id',
        'month',
        'actual_cost',
        'earned_value_percentage',
    ];

    protected $casts = [
        'actual_cost' => 'decimal:2',
        'earned_value_percentage' => 'decimal:2',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Calculate earned value for this month
     */
    public function getEarnedValueAttribute(): float
    {
        return ($this->earned_value_percentage / 100) * $this->task->amount;
    }
}
