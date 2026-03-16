<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'manager_id', 'name', 'address', 'city', 'county',
        'property_type', 'total_units', 'description', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
