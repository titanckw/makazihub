<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    protected $fillable = ['staff_id', 'year', 'allocated_days', 'used_days'];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function getRemainingDaysAttribute(): int
    {
        return max(0, $this->allocated_days - $this->used_days);
    }
}
