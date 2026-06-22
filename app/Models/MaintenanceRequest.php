<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRequest extends Model
{
    protected $fillable = [
        'tenant_id',
        'unit_id',
        'property_id',
        'repair_type',
        'description',
        'status',
        'priority',
        'requested_date',
        'completed_date',
        'notes',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'completed_date' => 'date',
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

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'under_review' => 'bg-blue-100 text-blue-800',
            'pending_repairs' => 'bg-amber-100 text-amber-800',
            'repair_review' => 'bg-purple-100 text-purple-800',
            'completed' => 'bg-emerald-100 text-emerald-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'under_review' => 'blue',
            'pending_repairs' => 'amber',
            'repair_review' => 'purple',
            'completed' => 'emerald',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'under_review' => 'Under Review',
            'pending_repairs' => 'Pending Repairs',
            'repair_review' => 'Repair Review',
            'completed' => 'Completed',
            default => 'Unknown',
        };
    }
}
