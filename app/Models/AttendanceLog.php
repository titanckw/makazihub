<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    protected $fillable = [
        'staff_id', 'date', 'clock_in', 'clock_out',
        'clock_in_lat', 'clock_in_lng', 'clock_out_lat', 'clock_out_lng',
        'status', 'notes',
    ];

    protected $casts = [
        'date'      => 'date',
        'clock_in'  => 'datetime',
        'clock_out' => 'datetime',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function getHoursWorkedAttribute(): ?float
    {
        if (!$this->clock_in || !$this->clock_out) {
            return null;
        }
        return round($this->clock_in->diffInMinutes($this->clock_out) / 60, 2);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'present'  => 'bg-success-bg text-success',
            'late'     => 'bg-warning-bg text-warning',
            'absent'   => 'bg-danger-bg text-danger',
            'on_leave' => 'bg-gray-100 text-gray-600',
            default    => 'bg-gray-100 text-gray-600',
        };
    }
}
