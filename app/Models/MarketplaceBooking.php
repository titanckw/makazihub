<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceBooking extends Model
{
    protected $fillable = [
        'tenant_id', 'service_provider_id', 'reference',
        'status', 'notes', 'preferred_date', 'contact_phone',
    ];

    protected $casts = [
        'preferred_date' => 'datetime',
    ];

    public static array $statuses = [
        'pending'     => ['label' => 'Pending',     'class' => 'bg-yellow-100 text-yellow-800'],
        'confirmed'   => ['label' => 'Confirmed',   'class' => 'bg-blue-100 text-blue-800'],
        'in_progress' => ['label' => 'In Progress', 'class' => 'bg-purple-100 text-purple-800'],
        'completed'   => ['label' => 'Completed',   'class' => 'bg-green-100 text-green-800'],
        'cancelled'   => ['label' => 'Cancelled',   'class' => 'bg-red-100 text-red-800'],
    ];

    protected static function booted(): void
    {
        static::creating(function (self $booking) {
            $booking->reference = 'MKT-' . str_pad(
                (self::max('id') ?? 0) + 1,
                5, '0', STR_PAD_LEFT
            );
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(MarketplaceServiceProvider::class, 'service_provider_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$statuses[$this->status]['label'] ?? ucfirst($this->status);
    }

    public function getStatusBadgeAttribute(): string
    {
        return self::$statuses[$this->status]['class'] ?? 'bg-gray-100 text-gray-800';
    }
}
