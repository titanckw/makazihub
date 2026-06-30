<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'user_id',
        'manager_id',
        'role',
        'id_number',
        'department',
        'employment_type',
        'start_date',
        'notes',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function documents()
    {
        return $this->hasMany(StaffDocument::class);
    }

    public function currentLeaveBalance(): ?LeaveBalance
    {
        return $this->leaveBalances()->where('year', now()->year)->first()
            ?? $this->leaveBalances()->create(['year' => now()->year, 'allocated_days' => 21]);
    }

    // -------------------------------------------------------
    // Accessors / Helpers
    // -------------------------------------------------------

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active'    => 'bg-success-bg text-success',
            'inactive'  => 'bg-warning-bg text-warning',
            'suspended' => 'bg-danger-bg text-danger',
            default     => 'bg-gray-100 text-gray-600',
        };
    }

    public function getEmploymentTypeLabelAttribute(): string
    {
        return match ($this->employment_type) {
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract'  => 'Contract',
            default     => ucfirst($this->employment_type),
        };
    }
}
