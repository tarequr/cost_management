<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DraftBudget extends Model
{
    protected $fillable = [
        'project_id', 'monthly_breakdown', 'total_amount', 'total_duration',
    ];

    protected $casts = [
        'monthly_breakdown' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
