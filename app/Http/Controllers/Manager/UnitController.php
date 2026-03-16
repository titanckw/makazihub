<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    public function create(Property $property)
    {
        $this->authorizeProperty($property);
        return view('manager.units.create', compact('property'));
    }

    public function store(Request $request, Property $property)
    {
        $this->authorizeProperty($property);

        $validated = $request->validate([
            'unit_number'    => 'required|string|max:50',
            'unit_type'      => 'required|in:studio,bedsitter,1br,2br,3br,4br,commercial,penthouse',
            'floor'          => 'nullable|integer|min:0|max:100',
            'rent_amount'    => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'description'    => 'nullable|string',
        ]);

        // Check unit number is unique within property
        $exists = Unit::where('property_id', $property->id)
            ->where('unit_number', $validated['unit_number'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['unit_number' => "Unit number '{$validated['unit_number']}' already exists in this property."]);
        }

        $validated['property_id'] = $property->id;
        $validated['status']      = 'vacant';

        Unit::create($validated);

        // Update total_units count on property
        $property->update(['total_units' => $property->units()->count()]);

        return redirect()
            ->route('manager.properties.show', $property)
            ->with('success', "Unit '{$validated['unit_number']}' added successfully.");
    }

    public function show(Property $property, Unit $unit)
    {
        $this->authorizeProperty($property);

        $unit->load(['activeLease.tenant.user', 'invoices' => fn($q) => $q->latest()->take(6)]);

        return view('manager.units.show', compact('property', 'unit'));
    }

    public function edit(Property $property, Unit $unit)
    {
        $this->authorizeProperty($property);
        return view('manager.units.edit', compact('property', 'unit'));
    }

    public function update(Request $request, Property $property, Unit $unit)
    {
        $this->authorizeProperty($property);

        $validated = $request->validate([
            'unit_number'    => 'required|string|max:50',
            'unit_type'      => 'required|in:studio,bedsitter,1br,2br,3br,4br,commercial,penthouse',
            'floor'          => 'nullable|integer|min:0|max:100',
            'rent_amount'    => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'status'         => 'required|in:vacant,occupied,maintenance',
            'description'    => 'nullable|string',
        ]);

        // Check unit number unique (excluding self)
        $exists = Unit::where('property_id', $property->id)
            ->where('unit_number', $validated['unit_number'])
            ->where('id', '!=', $unit->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['unit_number' => "Unit number '{$validated['unit_number']}' already exists in this property."]);
        }

        $unit->update($validated);

        return redirect()
            ->route('manager.properties.show', $property)
            ->with('success', "Unit '{$unit->unit_number}' updated successfully.");
    }

    public function destroy(Property $property, Unit $unit)
    {
        $this->authorizeProperty($property);

        if ($unit->status === 'occupied') {
            return back()->with('error', 'Cannot delete an occupied unit. Please terminate the lease first.');
        }

        $number = $unit->unit_number;
        $unit->delete();

        // Update total_units count
        $property->update(['total_units' => $property->units()->count()]);

        return redirect()
            ->route('manager.properties.show', $property)
            ->with('success', "Unit '{$number}' deleted successfully.");
    }

    private function authorizeProperty(Property $property)
    {
        if ($property->manager_id !== Auth::id()) {
            abort(403, 'You do not have access to this property.');
        }
    }
}
