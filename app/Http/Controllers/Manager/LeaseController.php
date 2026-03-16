<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaseController extends Controller
{
    /**
     * List all leases for manager's properties.
     */
    public function index(Request $request)
    {
        $manager = auth()->user();

        $query = Lease::with(['tenant.user', 'unit.property'])
            ->whereHas('unit.property', fn($q) => $q->where('manager_id', $manager->id));

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('property_id')) {
            $query->whereHas('unit', fn($q) => $q->where('property_id', $request->property_id));
        }

        // Flag leases expiring in the next 30 days
        $expiringCount = (clone $query)
            ->where('status', 'active')
            ->whereBetween('end_date', [now(), now()->addDays(30)])
            ->count();

        $leases     = $query->latest('start_date')->paginate(15)->withQueryString();
        $properties = Property::where('manager_id', $manager->id)->get();

        return view('manager.leases.index', compact('leases', 'properties', 'expiringCount'));
    }

    /**
     * Show form to create a new lease.
     */
    public function create(Request $request)
    {
        $manager = auth()->user();

        $units = Unit::with('property')
            ->whereHas('property', fn($q) => $q->where('manager_id', $manager->id))
            ->where('status', 'vacant')
            ->get();

        $tenants = Tenant::with('user')
            ->where('status', 'active')
            ->get();

        // Pre-select if tenant_id passed from tenant show page
        $selectedTenant = $request->filled('tenant_id')
            ? Tenant::with('user')->find($request->tenant_id)
            : null;

        return view('manager.leases.create', compact('units', 'tenants', 'selectedTenant'));
    }

    /**
     * Store a new lease.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tenant_id'       => 'required|exists:tenants,id',
            'unit_id'         => 'required|exists:units,id',
            'start_date'      => 'required|date',
            'end_date'        => 'required|date|after:start_date',
            'rent_amount'     => 'required|numeric|min:0',
            'deposit_amount'  => 'required|numeric|min:0',
            'payment_day'     => 'required|integer|min:1|max:28',
            'notes'           => 'nullable|string',
        ]);

        $manager = auth()->user();

        // Verify manager owns this unit
        $unit = Unit::whereHas('property', fn($q) => $q->where('manager_id', $manager->id))
            ->where('status', 'vacant')
            ->findOrFail($request->unit_id);

        DB::transaction(function () use ($request, $unit) {
            $lease = Lease::create([
                'tenant_id'      => $request->tenant_id,
                'unit_id'        => $unit->id,
                'property_id'    => $unit->property_id,
                'start_date'     => $request->start_date,
                'end_date'       => $request->end_date,
                'rent_amount'    => $request->rent_amount,
                'deposit_amount' => $request->deposit_amount,
                'payment_day'    => $request->payment_day,
                'status'         => 'active',
                'notes'          => $request->notes,
            ]);

            // Update unit status to occupied
            $unit->update([
                'status'    => 'occupied',
                'tenant_id' => $request->tenant_id,
            ]);

            // Also update tenant's current unit
            $lease->tenant->update(['unit_id' => $unit->id]);
        });

        return redirect()->route('manager.leases.index')
            ->with('success', 'Lease created successfully. You can now generate the first invoice.');
    }

    /**
     * Show lease details.
     */
    public function show(Lease $lease)
    {
        $this->authorizeManagerLease($lease);

        $lease->load(['tenant.user', 'unit.property', 'invoices.payments']);

        $totalExpected = $lease->invoices->sum('amount');
        $totalPaid     = $lease->invoices->flatMap->payments->sum('amount');
        $totalOutstanding = $totalExpected - $totalPaid;

        $daysLeft      = now()->diffInDays($lease->end_date, false);
        $isExpiringSoon = $daysLeft >= 0 && $daysLeft <= 30;

        return view('manager.leases.show', compact(
            'lease', 'totalExpected', 'totalPaid', 'totalOutstanding', 'daysLeft', 'isExpiringSoon'
        ));
    }

    /**
     * Show edit form.
     */
    public function edit(Lease $lease)
    {
        $this->authorizeManagerLease($lease);
        $lease->load(['tenant.user', 'unit.property']);
        return view('manager.leases.edit', compact('lease'));
    }

    /**
     * Update lease details (limited fields only — cannot change tenant/unit).
     */
    public function update(Request $request, Lease $lease)
    {
        $this->authorizeManagerLease($lease);

        $request->validate([
            'end_date'       => 'required|date|after:start_date',
            'rent_amount'    => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'payment_day'    => 'required|integer|min:1|max:28',
            'notes'          => 'nullable|string',
        ]);

        $lease->update([
            'end_date'       => $request->end_date,
            'rent_amount'    => $request->rent_amount,
            'deposit_amount' => $request->deposit_amount,
            'payment_day'    => $request->payment_day,
            'notes'          => $request->notes,
        ]);

        return redirect()->route('manager.leases.show', $lease)
            ->with('success', 'Lease updated successfully.');
    }

    /**
     * Terminate a lease early.
     */
    public function terminate(Request $request, Lease $lease)
    {
        $this->authorizeManagerLease($lease);

        $request->validate([
            'termination_reason' => 'required|string|max:500',
            'termination_date'   => 'required|date',
        ]);

        if ($lease->status !== 'active') {
            return back()->with('error', 'Only active leases can be terminated.');
        }

        DB::transaction(function () use ($request, $lease) {
            $lease->update([
                'status'             => 'terminated',
                'termination_reason' => $request->termination_reason,
                'terminated_at'      => $request->termination_date,
            ]);

            // Free up the unit
            $lease->unit->update([
                'status'    => 'vacant',
                'tenant_id' => null,
            ]);

            // Update tenant's unit reference
            $lease->tenant->update(['unit_id' => null]);
        });

        return redirect()->route('manager.leases.index')
            ->with('success', 'Lease terminated. Unit is now marked as vacant.');
    }

    /**
     * Renew a lease — creates a new lease from the old one's end date.
     */
    public function renew(Request $request, Lease $lease)
    {
        $this->authorizeManagerLease($lease);

        $request->validate([
            'new_end_date'   => 'required|date|after:' . $lease->end_date,
            'rent_amount'    => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $lease) {
            // Close the old lease
            $lease->update(['status' => 'expired']);

            // Create the new lease
            Lease::create([
                'tenant_id'      => $lease->tenant_id,
                'unit_id'        => $lease->unit_id,
                'property_id'    => $lease->property_id,
                'start_date'     => $lease->end_date,
                'end_date'       => $request->new_end_date,
                'rent_amount'    => $request->rent_amount,
                'deposit_amount' => $lease->deposit_amount,
                'payment_day'    => $lease->payment_day,
                'status'         => 'active',
                'notes'          => 'Renewed from lease #' . $lease->id,
            ]);
        });

        return redirect()->route('manager.leases.index')
            ->with('success', 'Lease renewed successfully.');
    }

    private function authorizeManagerLease(Lease $lease): void
    {
        $manager = auth()->user();
        $ok = Property::where('manager_id', $manager->id)
            ->where('id', $lease->property_id)
            ->exists();

        if (!$ok) abort(403);
    }
}
