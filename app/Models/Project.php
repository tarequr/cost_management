<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = ['name'];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function draftBudget()
    {
        return $this->hasOne(DraftBudget::class);
    }

    public function finalBudget()
    {
        return $this->hasOne(FinalBudget::class);
    }

    /**
     * Check if all tasks in the project have at least one actual cost record.
     */
    public function allTasksFinalized(): bool
    {
        if ($this->tasks->isEmpty()) {
            return false;
        }

        foreach ($this->tasks as $task) {
            if (!$task->monthlyActualCosts()->exists()) {
                return false;
            }
        }

        return true;
    }
}
