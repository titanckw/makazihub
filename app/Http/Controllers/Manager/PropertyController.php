<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::where('manager_id', Auth::id())
            ->withCount([
                'units',
                'units as occupied_count'    => fn($q) => $q->where('status', 'occupied'),
                'units as vacant_count'       => fn($q) => $q->where('status', 'vacant'),
                'units as maintenance_count'  => fn($q) => $q->where('status', 'maintenance'),
            ])
            ->latest()
            ->paginate(10);

        return view('manager.properties.index', compact('properties'));
    }

    public function create()
    {
        return view('manager.properties.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'address'       => 'required|string',
            'city'          => 'required|string|max:100',
            'county'        => 'required|string|max:100',
            'property_type' => 'required|in:apartment,maisonette,commercial,bedsitter,townhouse',
            'description'   => 'nullable|string',
        ]);

        $validated['manager_id'] = Auth::id();
        $validated['is_active']  = true;

        $property = Property::create($validated);

        return redirect()
            ->route('manager.properties.show', $property)
            ->with('success', "Property '{$property->name}' created successfully.");
    }

    public function show(Property $property)
    {
        $this->authorizeProperty($property);

        $units = $property->units()
            ->with(['activeLease.tenant.user'])
            ->orderBy('unit_number')
            ->get();

        $stats = [
            'total'       => $units->count(),
            'occupied'    => $units->where('status', 'occupied')->count(),
            'vacant'      => $units->where('status', 'vacant')->count(),
            'maintenance' => $units->where('status', 'maintenance')->count(),
            'monthly_income' => $units->where('status', 'occupied')->sum('rent_amount'),
        ];

        return view('manager.properties.show', compact('property', 'units', 'stats'));
    }

    public function edit(Property $property)
    {
        $this->authorizeProperty($property);
        return view('manager.properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        $this->authorizeProperty($property);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'address'       => 'required|string',
            'city'          => 'required|string|max:100',
            'county'        => 'required|string|max:100',
            'property_type' => 'required|in:apartment,maisonette,commercial,bedsitter,townhouse',
            'description'   => 'nullable|string',
            'is_active'     => 'boolean',
        ]);

        $property->update($validated);

        return redirect()
            ->route('manager.properties.show', $property)
            ->with('success', "Property '{$property->name}' updated successfully.");
    }

    public function destroy(Property $property)
    {
        $this->authorizeProperty($property);

        if ($property->units()->where('status', 'occupied')->exists()) {
            return back()->with('error', 'Cannot delete a property with occupied units. Please terminate all leases first.');
        }

        $name = $property->name;
        $property->delete();

        return redirect()
            ->route('manager.properties.index')
            ->with('success', "Property '{$name}' deleted successfully.");
    }

    private function authorizeProperty(Property $property)
    {
        if ($property->manager_id !== Auth::id()) {
            abort(403, 'You do not have access to this property.');
        }
    }
}
