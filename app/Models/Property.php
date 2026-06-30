<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'manager_id', 'name', 'address', 'city', 'county',
        'property_type', 'total_units', 'description', 'is_active',
        'landlord_tax_status', 'is_vat_registered', 'landlord_pin',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_vat_registered' => 'boolean',
    ];

    public function isCommercial(): bool
    {
        return $this->property_type === 'commercial';
    }

    public function isNonResidentLandlord(): bool
    {
        return $this->landlord_tax_status === 'non_resident';
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }
}
