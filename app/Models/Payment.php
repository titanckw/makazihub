<?php
// app/Models/Payment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'tenant_id',
        'amount',
        'payment_method',  // mpesa | cash | bank_transfer | cheque
        'reference',       // M-Pesa code, cheque no, etc.
        'mpesa_receipt',
        'phone_number',
        'status',          // pending | confirmed | reversed
        'payment_date',
        'paid_at',
        'confirmed_at',
        'notes',
        'recorded_by',     // user_id of manager who recorded it
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'confirmed' => 'bg-success-bg text-success',
            'reversed' => 'bg-danger-bg text-danger',
            default => 'bg-warning-bg text-warning',
        };
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'mpesa' => 'M-Pesa',
            'bank_transfer' => 'Bank Transfer',
            'cheque' => 'Cheque',
            default => 'Cash',
        };
    }
}
