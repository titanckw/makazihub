<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // Relationships
    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'manager_id');
    }

    // Helper: get first name only
    public function getFirstNameAttribute(): string
    {
        return explode(' ', $this->name)[0];
    }

    // Helper: get role label
    public function getRoleLabelAttribute(): string
    {
        return match(true) {
            $this->hasRole('superadmin') => 'Super Admin',
            $this->hasRole('manager')    => 'Manager',
            $this->hasRole('tenant')     => 'Tenant',
            default                      => 'Unknown',
        };
    }
}
