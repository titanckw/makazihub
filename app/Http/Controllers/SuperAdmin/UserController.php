<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private array $defaultPasswords = [
        'manager' => 'Manager@1234',
        'staff'   => 'Staff@1234',
        'tenant'  => 'Tenant@1234',
        'superadmin' => 'Admin@1234',
    ];

    /**
     * List all platform users, filterable by role/status.
     */
    public function index(Request $request)
    {
        $query = User::with('roles')->latest();

        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%"));
        }

        $users = $query->paginate(20)->withQueryString();

        return view('superadmin.users.index', compact('users'));
    }

    /**
     * Show the create-user form.
     */
    public function create()
    {
        $managers = User::role('manager')->orderBy('name')->get();
        return view('superadmin.users.create', compact('managers'));
    }

    /**
     * Create a user with the given role (manager, staff, tenant, or superadmin)
     * and their corresponding profile record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role'  => 'required|in:manager,staff,tenant,superadmin',
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',

            // Staff-specific
            'manager_id'      => 'required_if:role,staff,tenant|nullable|exists:users,id',
            'staff_role'      => 'required_if:role,staff|nullable|string|max:100',
            'department'      => 'nullable|string|max:100',
            'employment_type' => 'required_if:role,staff|nullable|in:full_time,part_time,contract',

            // Tenant-specific
            'id_number'               => 'required_if:role,tenant|nullable|string|max:50|unique:tenants,id_number',
            'emergency_contact_name'  => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'occupation'              => 'nullable|string|max:255',
            'employer'                => 'nullable|string|max:255',
        ]);

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'phone'    => $request->phone,
                'password' => Hash::make($this->defaultPasswords[$request->role]),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            $role = Role::firstOrCreate(['name' => $request->role, 'guard_name' => 'web']);
            $user->assignRole($role);

            if ($request->role === 'staff') {
                Staff::create([
                    'user_id'         => $user->id,
                    'manager_id'      => $request->manager_id,
                    'role'            => $request->staff_role,
                    'id_number'       => $request->id_number,
                    'department'      => $request->department,
                    'employment_type' => $request->employment_type,
                    'start_date'      => now(),
                    'status'          => 'active',
                ]);
            }

            if ($request->role === 'tenant') {
                Tenant::create([
                    'user_id'                 => $user->id,
                    'manager_id'               => $request->manager_id,
                    'id_number'                => $request->id_number,
                    'emergency_contact_name'   => $request->emergency_contact_name,
                    'emergency_contact_phone'  => $request->emergency_contact_phone,
                    'occupation'               => $request->occupation,
                    'employer'                 => $request->employer,
                    'status'                   => 'active',
                ]);
            }

            return $user;
        });

        return redirect()->route('superadmin.users.show', $user)
            ->with('success', "User '{$user->name}' created as " . ucfirst($request->role)
                . ". Default password: " . $this->defaultPasswords[$request->role]
                . ($request->role === 'tenant' ? ' (assign a unit/lease from the manager portal to activate their tenancy.)' : ''));
    }

    /**
     * Show a single user's profile.
     */
    public function show(User $user)
    {
        $user->load('roles');
        $profile = match (true) {
            $user->hasRole('staff')  => $user->staffProfile()->with('manager')->first(),
            $user->hasRole('tenant') => $user->tenant()->with(['manager', 'unit.property'])->first(),
            default => null,
        };

        return view('superadmin.users.show', compact('user', 'profile'));
    }

    /**
     * Show the edit form.
     */
    public function edit(User $user)
    {
        $user->load('roles');
        $managers = User::role('manager')->orderBy('name')->get();
        $profile = match (true) {
            $user->hasRole('staff')  => $user->staffProfile,
            $user->hasRole('tenant') => $user->tenant,
            default => null,
        };

        return view('superadmin.users.edit', compact('user', 'managers', 'profile'));
    }

    /**
     * Update basic account fields (and limited profile fields).
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $user->update($request->only('name', 'email', 'phone'));

        if ($request->filled('manager_id')) {
            if ($user->hasRole('staff') && $user->staffProfile) {
                $user->staffProfile->update(['manager_id' => $request->manager_id]);
            }
            if ($user->hasRole('tenant') && $user->tenant) {
                $user->tenant->update(['manager_id' => $request->manager_id]);
            }
        }

        return redirect()->route('superadmin.users.show', $user)
            ->with('success', "User '{$user->name}' updated successfully.");
    }

    /**
     * Activate / deactivate a user account (soft block, no deletion).
     */
    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', $user->name . ' is now ' . ($user->is_active ? 'active' : 'inactive') . '.');
    }

    /**
     * Delete a user account. Blocked if the user has dependent records that
     * would orphan financial or operational data.
     */
    public function destroy(User $user)
    {
        if ($user->hasRole('superadmin')) {
            return back()->with('error', 'SuperAdmin accounts cannot be deleted.');
        }

        if ($user->hasRole('manager') && $user->properties()->exists()) {
            return back()->with('error', 'Cannot delete a manager who still owns properties. Reassign them first.');
        }

        $user->delete();

        return redirect()->route('superadmin.users.index')->with('success', 'User deleted.');
    }
}
