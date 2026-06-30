<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $fillable = [
        'staff_id', 'type', 'start_date', 'end_date', 'days',
        'reason', 'status', 'manager_comment', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'approved'  => 'bg-success-bg text-success',
            'rejected'  => 'bg-danger-bg text-danger',
            'cancelled' => 'bg-gray-100 text-gray-600',
            default     => 'bg-warning-bg text-warning',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'annual'        => 'Annual Leave',
            'sick'          => 'Sick Leave',
            'unpaid'        => 'Unpaid Leave',
            'compassionate' => 'Compassionate Leave',
            default         => ucfirst($this->type),
        };
    }
}
