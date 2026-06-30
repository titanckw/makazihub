<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    /**
     * List all staff belonging to this manager.
     */
    public function index(Request $request)
    {
        $manager = Auth::user();

        $query = Staff::query()
            ->with('user')
            ->where('manager_id', $manager->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) =>
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
            )->orWhere('id_number', 'like', "%$search%");
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $staff = $query->latest()->paginate(15)->withQueryString();

        // Distinct roles already in use by this manager for the filter dropdown
        $roles = Staff::where('manager_id', $manager->id)
            ->distinct()
            ->pluck('role')
            ->sort()
            ->values();

        return view('manager.staff.index', compact('staff', 'roles'));
    }

    /**
     * Show the create staff form.
     */
    public function create()
    {
        return view('manager.staff.create');
    }

    /**
     * Store a new staff member and create their user account.
     */
    public function store(Request $request)
    {
        $manager = Auth::user();

        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'phone'           => 'required|string|max:20',
            'role'            => 'required|string|max:100',
            'id_number'       => 'nullable|string|max:20',
            'department'      => 'nullable|string|max:100',
            'employment_type' => 'required|in:full_time,part_time,contract',
            'start_date'      => 'nullable|date',
            'notes'           => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $manager) {
            // Create the user account
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'phone'    => $request->phone,
                'password' => Hash::make('Staff@1234'),
            ]);

            // Assign the 'staff' Spatie role
            $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
            $user->assignRole($staffRole);

            // Create the staff profile
            Staff::create([
                'user_id'         => $user->id,
                'manager_id'      => $manager->id,
                'role'            => $request->role,
                'id_number'       => $request->id_number,
                'department'      => $request->department,
                'employment_type' => $request->employment_type,
                'start_date'      => $request->start_date,
                'notes'           => $request->notes,
                'status'          => 'active',
            ]);
        });

        return redirect()->route('manager.staff.index')
            ->with('success', 'Staff member added successfully. Default password: Staff@1234');
    }

    /**
     * Show a single staff member.
     */
    public function show(Staff $staff)
    {
        $this->authorizeAccess($staff);
        $staff->load('user');
        return view('manager.staff.show', compact('staff'));
    }

    /**
     * Show the edit form.
     */
    public function edit(Staff $staff)
    {
        $this->authorizeAccess($staff);
        $staff->load('user');
        return view('manager.staff.edit', compact('staff'));
    }

    /**
     * Update a staff member.
     */
    public function update(Request $request, Staff $staff)
    {
        $this->authorizeAccess($staff);

        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email,' . $staff->user_id,
            'phone'           => 'nullable|string|max:20',
            'role'            => 'required|string|max:100',
            'id_number'       => 'nullable|string|max:20',
            'department'      => 'nullable|string|max:100',
            'employment_type' => 'required|in:full_time,part_time,contract',
            'start_date'      => 'nullable|date',
            'notes'           => 'nullable|string',
            'status'          => 'required|in:active,inactive,suspended',
        ]);

        DB::transaction(function () use ($request, $staff) {
            $staff->user->update([
                'name'  => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            $staff->update([
                'role'            => $request->role,
                'id_number'       => $request->id_number,
                'department'      => $request->department,
                'employment_type' => $request->employment_type,
                'start_date'      => $request->start_date,
                'notes'           => $request->notes,
                'status'          => $request->status,
            ]);
        });

        return redirect()->route('manager.staff.show', $staff)
            ->with('success', 'Staff member updated successfully.');
    }

    /**
     * Delete a staff member and their user account.
     */
    public function destroy(Staff $staff)
    {
        $this->authorizeAccess($staff);

        DB::transaction(function () use ($staff) {
            $user = $staff->user;
            $staff->delete();
            $user->delete();
        });

        return redirect()->route('manager.staff.index')
            ->with('success', 'Staff member removed.');
    }

    // -------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------

    private function authorizeAccess(Staff $staff): void
    {
        if ($staff->manager_id !== Auth::id()) {
            abort(403, 'You do not have access to this staff member.');
        }
    }
}
