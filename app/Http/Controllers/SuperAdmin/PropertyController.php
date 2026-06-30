<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    /**
     * List all properties across all managers (platform-wide view).
     */
    public function index(Request $request)
    {
        $query = Property::with('manager')
            ->withCount([
                'units',
                'units as occupied_count' => fn ($q) => $q->where('status', 'occupied'),
                'units as vacant_count'   => fn ($q) => $q->where('status', 'vacant'),
            ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('city', 'like', "%$search%")
                ->orWhere('county', 'like', "%$search%"));
        }

        if ($request->filled('manager_id')) {
            $query->where('manager_id', $request->manager_id);
        }

        if ($request->filled('type')) {
            $query->where('property_type', $request->type);
        }

        $properties = $query->latest()->paginate(15)->withQueryString();

        $managers = \App\Models\User::role('manager')->orderBy('name')->get();

        return view('superadmin.properties.index', compact('properties', 'managers'));
    }

    /**
     * Show a single property (read-only, platform-wide view).
     */
    public function show(Property $property)
    {
        $property->load('manager');

        $units = $property->units()
            ->with(['activeLease.tenant.user'])
            ->orderBy('unit_number')
            ->get();

        $stats = [
            'total'           => $units->count(),
            'occupied'        => $units->where('status', 'occupied')->count(),
            'vacant'          => $units->where('status', 'vacant')->count(),
            'maintenance'     => $units->where('status', 'maintenance')->count(),
            'monthly_income'  => $units->where('status', 'occupied')->sum('rent_amount'),
        ];

        return view('superadmin.properties.show', compact('property', 'units', 'stats'));
    }
}
