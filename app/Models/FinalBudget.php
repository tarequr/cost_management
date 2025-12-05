<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalBudget extends Model
{
    protected $fillable = [
        'project_id', 'PV', 'AC', 'EV', 'SPI', 'CPI', 'CV', 'SV', 'BAC', 'ETC', 'EAC',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
