<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetCalculator extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'from_date' => 'datetime',
        'to_date' => 'datetime',
    ];
}
