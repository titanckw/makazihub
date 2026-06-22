<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    public function index()
    {
        $managerId = Auth::id();
        $propertyIds = Property::where('manager_id', $managerId)->pluck('id');
        $unitIds = Unit::whereIn('property_id', $propertyIds)->pluck('id');

        $maintenanceRequests = MaintenanceRequest::whereIn('unit_id', $unitIds)
            ->with(['tenant.user', 'unit', 'property'])
            ->latest()
            ->paginate(15);

        $stats = [
            'under_review' => MaintenanceRequest::whereIn('unit_id', $unitIds)->where('status', 'under_review')->count(),
            'pending_repairs' => MaintenanceRequest::whereIn('unit_id', $unitIds)->where('status', 'pending_repairs')->count(),
            'repair_review' => MaintenanceRequest::whereIn('unit_id', $unitIds)->where('status', 'repair_review')->count(),
            'completed' => MaintenanceRequest::whereIn('unit_id', $unitIds)->where('status', 'completed')->count(),
        ];

        return view('manager.maintenance.index', compact('maintenanceRequests', 'stats'));
    }

    public function show(MaintenanceRequest $maintenance)
    {
        $managerId = Auth::id();
        $propertyIds = Property::where('manager_id', $managerId)->pluck('id');

        if (!$propertyIds->contains($maintenance->property_id)) {
            abort(403, 'Unauthorized access.');
        }

        return view('manager.maintenance.show', compact('maintenance'));
    }

    public function updateStatus(MaintenanceRequest $maintenance)
    {
        $managerId = Auth::id();
        $propertyIds = Property::where('manager_id', $managerId)->pluck('id');

        if (!$propertyIds->contains($maintenance->property_id)) {
            abort(403, 'Unauthorized access.');
        }

        $validated = request()->validate([
            'status' => 'required|in:under_review,pending_repairs,repair_review,completed',
            'notes' => 'nullable|string',
        ]);

        $maintenance->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $maintenance->notes,
            'completed_date' => $validated['status'] === 'completed' ? today() : $maintenance->completed_date,
        ]);

        return redirect()->route('manager.maintenance.show', $maintenance)->with('success', 'Maintenance request updated.');
    }
}
