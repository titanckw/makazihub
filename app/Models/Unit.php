<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'property_id', 'unit_number', 'unit_type', 'floor',
        'rent_amount', 'deposit_amount', 'status', 'description',
    ];

    protected $casts = [
        'rent_amount'    => 'decimal:2',
        'deposit_amount' => 'decimal:2',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    public function activeLease()
    {
        return $this->hasOne(Lease::class)->where('status', 'active');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    // Status badge color helper
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'occupied'    => 'bg-emerald-100 text-emerald-700',
            'vacant'      => 'bg-blue-100 text-blue-700',
            'maintenance' => 'bg-amber-100 text-amber-700',
            default       => 'bg-gray-100 text-gray-700',
        };
    }
}
