<?php
// app/Models/Invoice.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Invoice extends Model
{
    protected $fillable = [
        'lease_id',
        'tenant_id',
        'unit_id',
        'property_id',
        'invoice_number',
        'amount_due',
        'late_fee',
        'total_amount',
        'amount_paid',
        'balance',
        'due_date',
        'period_start',
        'period_end',
        'invoice_date',
        'billing_period',
        'status',       // unpaid | partial | paid | overdue
        'notes',
        'expected_completion_date',
        'generated_by', // manual | auto
    ];

    protected $casts = [
        'due_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'expected_completion_date' => 'date',
        'amount_due' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

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

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    // ── Computed Attributes ────────────────────────────────────────

    public function getBalanceAttribute(): float
    {
        return max(0, ($this->total_amount ?? 0) - ($this->amount_paid ?? 0));
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && Carbon::parse($this->due_date)->isPast() && $this->status !== 'paid';
    }

    /**
     * Tailwind badge classes matching the MakaziHub color system.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'bg-success-bg text-success',
            'partial' => 'bg-info-bg text-info',
            'overdue' => 'bg-danger-bg text-danger',
            default => 'bg-warning-bg text-warning',   // unpaid
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'Paid',
            'partial' => 'Partial',
            'overdue' => 'Overdue',
            default => 'Unpaid',
        };
    }

    // ── Static Helpers ─────────────────────────────────────────────

    /**
     * Generate a unique invoice number e.g. INV-2026-00042
     */
    /**
     * Legacy alias used throughout views and services. Return total_amount.
     */
    public function getAmountAttribute(): float
    {
        return (float) ($this->total_amount ?? 0);
    }

    public static function generateNumber(): string
    {
        $year = now()->year;
        $count = static::whereYear('created_at', $year)->count() + 1;
        return 'INV-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}
