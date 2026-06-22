<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class TenantController extends Controller
{
    /**
     * List all tenants across manager's properties.
     */
    public function index(Request $request)
    {
        $manager = Auth::user();

        $query = Tenant::query()
            ->with(['user', 'unit.property'])
            ->where('tenants.manager_id', $manager->id);

        // Filter by property
        if ($request->filled('property_id')) {
            $query->whereHas('unit', fn($q) => $q->where('property_id', $request->property_id));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name or ID number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%"))
                ->orWhere('id_number', 'like', "%$search%");
        }

        $tenants = $query->latest()->paginate(15)->withQueryString();

        $properties = Property::where('manager_id', $manager->id)->get();

        return view('manager.tenants.index', compact('tenants', 'properties'));
    }

    /**
     * Show the create tenant form.
     */
    public function create()
    {
        $manager = Auth::user();

        // Only show vacant units
        $units = Unit::query()
            ->with('property')
            ->whereHas('property', fn($q) => $q->where('manager_id', $manager->id))
            ->where('status', 'vacant')
            ->get();

        return view('manager.tenants.create', compact('units'));
    }

    /**
     * Store a new tenant and optionally create a user account.
     */
    public function store(Request $request)
    {
        $manager = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'id_number' => 'required|string|max:50|unique:tenants,id_number',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'occupation' => 'nullable|string|max:255',
            'employer' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $manager) {
            // Create the user account
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make('Tenant@1234'), // default password
            ]);

            $tenantRole = Role::findByName('tenant');
            $user->assignRole($tenantRole);

            // Create the tenant profile
            Tenant::create([
                'user_id' => $user->id,
                'manager_id' => $manager->id,
                'id_number' => $request->id_number,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'occupation' => $request->occupation,
                'employer' => $request->employer,
                'notes' => $request->notes,
                'status' => 'active',
            ]);
        });

        return redirect()->route('manager.tenants.index')
            ->with('success', 'Tenant created successfully. Default password: Tenant@1234');
    }

    /**
     * Show tenant details.
     */
    public function show(Tenant $tenant)
    {
        $this->authorizeManagerAccess($tenant);

        $tenant->load([
            'user',
            'leases.unit.property',
            'leases.invoices',
        ]);

        $activeLeases = $tenant->leases->where('status', 'active');
        $totalPaid = $tenant->leases->flatMap->invoices->where('status', 'paid')->sum('amount');
        $totalOverdue = $tenant->leases->flatMap->invoices->where('status', 'overdue')->sum('amount');

        return view('manager.tenants.show', compact('tenant', 'activeLeases', 'totalPaid', 'totalOverdue'));
    }

    /**
     * Show edit form.
     */
    public function edit(Tenant $tenant)
    {
        $this->authorizeManagerAccess($tenant);
        $tenant->load('user');
        return view('manager.tenants.edit', compact('tenant'));
    }

    /**
     * Update tenant profile.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $this->authorizeManagerAccess($tenant);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $tenant->user_id,
            'phone' => 'nullable|string|max:20',
            'id_number' => 'required|string|max:50|unique:tenants,id_number,' . $tenant->id,
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'occupation' => 'nullable|string|max:255',
            'employer' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,blacklisted',
        ]);

        DB::transaction(function () use ($request, $tenant) {
            $tenant->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            $tenant->update([
                'id_number' => $request->id_number,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'occupation' => $request->occupation,
                'employer' => $request->employer,
                'notes' => $request->notes,
                'status' => $request->status,
            ]);
        });

        return redirect()->route('manager.tenants.show', $tenant)
            ->with('success', 'Tenant updated successfully.');
    }

    /**
     * Ensure the authenticated manager owns a property this tenant lives in.
     */
    private function authorizeManagerAccess(Tenant $tenant): void
    {
        $manager = Auth::user();
        $hasAccess = $tenant->leases()
            ->whereHas('unit.property', fn($q) => $q->where('manager_id', $manager->id))
            ->exists();

        // Allow if tenant has no leases yet (newly created)
        if ($tenant->leases()->count() > 0 && !$hasAccess) {
            abort(403, 'You do not manage a property this tenant belongs to.');
        }
    }
}
