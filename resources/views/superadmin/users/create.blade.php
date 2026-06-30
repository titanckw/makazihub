@extends('layouts.app')

@section('title', 'Add User')
@section('page-title', 'Add User')
@section('page-subtitle', 'Create a manager, staff, tenant, or superadmin account')

@section('sidebar-nav')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('superadmin.users.store') }}" class="space-y-6" id="user-create-form">
        @csrf

        {{-- Role --}}
        <div class="bg-white rounded-2xl border border-border p-6 space-y-5">
            <h2 class="text-sm font-semibold text-primary">Account Type</h2>
            <div>
                <label class="block text-xs font-semibold text-secondary mb-1.5">Role <span class="text-danger">*</span></label>
                <select name="role" id="role-select" onchange="toggleRoleFields()" required
                    class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400
                           @error('role') border-danger @enderror">
                    <option value="manager" {{ old('role', 'manager') === 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="tenant" {{ old('role') === 'tenant' ? 'selected' : '' }}>Tenant</option>
                    <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                </select>
                @error('role')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-muted mt-1">A default password is set automatically and shown after creation. The user should change it on first login.</p>
            </div>
        </div>

        {{-- Personal Details --}}
        <div class="bg-white rounded-2xl border border-border p-6 space-y-5">
            <h2 class="text-sm font-semibold text-primary">Personal Details</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400
                               @error('name') border-danger @enderror">
                    @error('name')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400
                               @error('phone') border-danger @enderror">
                    @error('phone')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-secondary mb-1.5">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400
                           @error('email') border-danger @enderror">
                @error('email')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-muted mt-1">This will be their login email.</p>
            </div>
        </div>

        {{-- Manager assignment (staff/tenant only) --}}
        <div class="bg-white rounded-2xl border border-border p-6 space-y-5" id="manager-fields">
            <h2 class="text-sm font-semibold text-primary">Reporting Manager</h2>
            <div>
                <label class="block text-xs font-semibold text-secondary mb-1.5">Manager <span class="text-danger">*</span></label>
                <select name="manager_id"
                    class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400
                           @error('manager_id') border-danger @enderror">
                    <option value="">Select manager...</option>
                    @foreach($managers as $manager)
                        <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                    @endforeach
                </select>
                @error('manager_id')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                @if($managers->isEmpty())
                    <p class="text-xs text-warning mt-1">No managers exist yet — create a manager account first.</p>
                @endif
            </div>
        </div>

        {{-- Staff-specific --}}
        <div class="bg-white rounded-2xl border border-border p-6 space-y-5" id="staff-fields">
            <h2 class="text-sm font-semibold text-primary">Employment Details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Role / Job Title</label>
                    <input type="text" name="staff_role" value="{{ old('staff_role') }}" placeholder="e.g. Caretaker, Security"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                    @error('staff_role')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Department</label>
                    <input type="text" name="department" value="{{ old('department') }}" placeholder="e.g. Maintenance"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Employment Type</label>
                    <select name="employment_type" class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                        <option value="full_time" {{ old('employment_type') === 'full_time' ? 'selected' : '' }}>Full Time</option>
                        <option value="part_time" {{ old('employment_type') === 'part_time' ? 'selected' : '' }}>Part Time</option>
                        <option value="contract" {{ old('employment_type') === 'contract' ? 'selected' : '' }}>Contract</option>
                    </select>
                    @error('employment_type')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Tenant-specific --}}
        <div class="bg-white rounded-2xl border border-border p-6 space-y-5" id="tenant-fields">
            <h2 class="text-sm font-semibold text-primary">Tenant Details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-secondary mb-1.5">ID Number</label>
                    <input type="text" name="id_number" value="{{ old('id_number') }}"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                    @error('id_number')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Occupation</label>
                    <input type="text" name="occupation" value="{{ old('occupation') }}"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Employer</label>
                    <input type="text" name="employer" value="{{ old('employer') }}"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Emergency Contact Phone</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
            </div>
            <p class="text-xs text-muted">A unit and lease are assigned later from the manager's tenant screen — this just creates their login account and profile.</p>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            <button type="submit"
                class="bg-brand-600 hover:bg-brand-500 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                Create User
            </button>
            <a href="{{ route('superadmin.users.index') }}"
                class="text-sm border border-border text-secondary hover:bg-surface px-5 py-2.5 rounded-xl transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function toggleRoleFields() {
    const role = document.getElementById('role-select').value;
    document.getElementById('manager-fields').style.display = (role === 'staff' || role === 'tenant') ? '' : 'none';
    document.getElementById('staff-fields').style.display = role === 'staff' ? '' : 'none';
    document.getElementById('tenant-fields').style.display = role === 'tenant' ? '' : 'none';
}
document.addEventListener('DOMContentLoaded', toggleRoleFields);
</script>
@endpush
