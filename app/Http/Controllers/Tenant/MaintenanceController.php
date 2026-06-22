<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    public const COMMON_REPAIRS = [
        'plumbing' => 'Plumbing Issues',
        'electrical' => 'Electrical Problems',
        'carpentry' => 'Carpentry/Wood Work',
        'painting' => 'Painting',
        'air_conditioning' => 'Air Conditioning',
        'heating' => 'Heating',
        'roofing' => 'Roofing',
        'windows' => 'Windows/Doors',
        'appliances' => 'Kitchen Appliances',
        'fixtures' => 'Fixtures & Fittings',
        'flooring' => 'Flooring',
        'walls' => 'Walls & Ceilings',
        'locks' => 'Locks & Hardware',
        'cleaning' => 'Cleaning/General Maintenance',
        'other' => 'Other',
    ];

    public function index()
    {
        $userId = Auth::id();
        $tenant = Tenant::where('user_id', $userId)->first();

        if (!$tenant) {
            return redirect()->route('tenant.dashboard')->with('error', 'Tenant profile not found.');
        }

        $maintenanceRequests = MaintenanceRequest::where('tenant_id', $tenant->id)
            ->with(['unit', 'property'])
            ->latest()
            ->paginate(10);

        return view('tenant.maintenance.index', compact('maintenanceRequests'));
    }

    public function create()
    {
        return view('tenant.maintenance.create', ['repairTypes' => self::COMMON_REPAIRS]);
    }

    public function store()
    {
        $userId = Auth::id();
        $tenant = Tenant::where('user_id', $userId)->first();

        if (!$tenant) {
            return redirect()->route('tenant.dashboard')->with('error', 'Tenant profile not found.');
        }

        if (!$tenant->unit_id || !$tenant->unit) {
            return redirect()->route('tenant.maintenance.index')->with('error', 'You must be assigned to a unit before submitting maintenance requests.');
        }

        $validated = request()->validate([
            'repair_type' => 'required|string',
            'description' => 'required|string|max:1000',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        MaintenanceRequest::create([
            'tenant_id' => $tenant->id,
            'unit_id' => $tenant->unit_id,
            'property_id' => $tenant->unit->property_id,
            'repair_type' => $validated['repair_type'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'under_review',
            'requested_date' => today(),
        ]);

        return redirect()->route('tenant.maintenance.index')->with('success', 'Maintenance request submitted successfully.');
    }

    public function show(MaintenanceRequest $maintenance)
    {
        $userId = Auth::id();
        $tenant = Tenant::where('user_id', $userId)->first();

        if (!$tenant || $maintenance->tenant_id !== $tenant->id) {
            return redirect()->route('tenant.maintenance.index')->with('error', 'Unauthorized access.');
        }

        return view('tenant.maintenance.show', compact('maintenance'));
    }
}
