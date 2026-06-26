<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceServiceProvider extends Model
{
    protected $fillable = [
        'name', 'slug', 'category', 'description', 'logo',
        'phone', 'whatsapp', 'email', 'website', 'working_hours',
        'base_price', 'price_label', 'is_active', 'is_featured',
        'sort_order', 'property_id',
    ];

    protected $casts = [
        'working_hours' => 'array',
        'is_active'     => 'boolean',
        'is_featured'   => 'boolean',
        'base_price'    => 'decimal:2',
    ];

    // ── Category meta ─────────────────────────────────────────────────────────

    public static array $categories = [
        'laundry'       => ['label' => 'Laundry',         'icon' => '👕', 'color' => 'blue'],
        'gas_delivery'  => ['label' => 'Gas Delivery',    'icon' => '🔥', 'color' => 'orange'],
        'mama_fua'      => ['label' => 'Mama Fua',        'icon' => '🧺', 'color' => 'purple'],
        'shopping'      => ['label' => 'Shopping',        'icon' => '🛒', 'color' => 'green'],
        'cleaning'      => ['label' => 'Cleaning',        'icon' => '🧹', 'color' => 'teal'],
        'food_delivery' => ['label' => 'Food Delivery',   'icon' => '🍱', 'color' => 'red'],
        'handyman'      => ['label' => 'Handyman',        'icon' => '🔧', 'color' => 'yellow'],
        'other'         => ['label' => 'Other',           'icon' => '📦', 'color' => 'gray'],
    ];

    public function getCategoryLabelAttribute(): string
    {
        return self::$categories[$this->category]['label'] ?? ucfirst($this->category);
    }

    public function getCategoryIconAttribute(): string
    {
        return self::$categories[$this->category]['icon'] ?? '📦';
    }

    public function getCategoryColorAttribute(): string
    {
        return self::$categories[$this->category]['color'] ?? 'gray';
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(MarketplaceBooking::class, 'service_provider_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeForProperty($query, ?int $propertyId)
    {
        return $query->where(function ($q) use ($propertyId) {
            $q->whereNull('property_id')
              ->orWhere('property_id', $propertyId);
        });
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // ── Contact helpers ───────────────────────────────────────────────────────

    public function getWhatsappUrlAttribute(): ?string
    {
        $number = $this->whatsapp ?? $this->phone;
        if (!$number) return null;
        $cleaned = preg_replace('/[^0-9]/', '', $number);
        // Convert 07xx to 2547xx
        if (str_starts_with($cleaned, '07') || str_starts_with($cleaned, '01')) {
            $cleaned = '254' . substr($cleaned, 1);
        }
        return "https://wa.me/{$cleaned}";
    }

    public function getCallUrlAttribute(): string
    {
        return "tel:{$this->phone}";
    }
}
