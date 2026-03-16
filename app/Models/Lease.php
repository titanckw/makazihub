<?php
// app/Models/Lease.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Lease extends Model
{
    protected $fillable = [
        'tenant_id',
        'unit_id',
        'property_id',
        'start_date',
        'end_date',
        'rent_amount',
        'deposit_amount',
        'payment_day',
        'status',
        'notes',
        'termination_reason',
        'terminated_at',
    ];

    protected $casts = [
        'start_date'     => 'date',
        'end_date'       => 'date',
        'terminated_at'  => 'date',
        'rent_amount'    => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Days remaining until lease expires. Negative = already expired.
     */
    public function getDaysRemainingAttribute(): int
    {
        return (int) now()->diffInDays($this->end_date, false);
    }

    /**
     * True if lease expires within 30 days.
     */
    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->days_remaining >= 0 && $this->days_remaining <= 30;
    }

    /**
     * Tailwind classes for status badge.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active'      => 'bg-success-bg text-success',
            'expired'     => 'bg-warning-bg text-warning',
            'terminated'  => 'bg-danger-bg text-danger',
            default       => 'bg-gray-100 text-gray-600',
        };
    }

    /**
     * Human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'     => 'Active',
            'expired'    => 'Expired',
            'terminated' => 'Terminated',
            default      => ucfirst($this->status),
        };
    }
}
