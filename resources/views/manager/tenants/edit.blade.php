@extends('layouts.app')

@section('title', 'Edit Tenant')
@section('page-title', 'Edit Tenant')
@section('page-subtitle', $tenant->user->name)

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <a href="{{ route('manager.tenants.show', $tenant) }}" class="inline-flex items-center gap-2 text-sm text-secondary hover:text-primary transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Tenant
    </a>

    <form action="{{ route('manager.tenants.update', $tenant) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-border p-6 space-y-5">
            <h2 class="text-sm font-bold text-primary uppercase tracking-wider">Personal Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $tenant->user->name) }}" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">ID / Passport Number <span class="text-danger">*</span></label>
                    <input type="text" name="id_number" value="{{ old('id_number', $tenant->id_number) }}" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $tenant->user->email) }}" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $tenant->user->phone) }}"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Occupation</label>
                    <input type="text" name="occupation" value="{{ old('occupation', $tenant->occupation) }}"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Employer</label>
                    <input type="text" name="employer" value="{{ old('employer', $tenant->employer) }}"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-secondary mb-1.5">Status <span class="text-danger">*</span></label>
                <select name="status" required
                    class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                    <option value="active"      {{ old('status', $tenant->status) === 'active'      ? 'selected' : '' }}>Active</option>
                    <option value="inactive"    {{ old('status', $tenant->status) === 'inactive'    ? 'selected' : '' }}>Inactive</option>
                    <option value="blacklisted" {{ old('status', $tenant->status) === 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
                </select>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-border p-6 space-y-4">
            <h2 class="text-sm font-bold text-primary uppercase tracking-wider">Emergency Contact</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Name</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $tenant->emergency_contact_name) }}"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">Phone</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $tenant->emergency_contact_phone) }}"
                        class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-border p-6">
            <label class="block text-sm font-medium text-secondary mb-1.5">Notes</label>
            <textarea name="notes" rows="3"
                class="w-full text-sm border border-border rounded-xl px-3 py-2.5 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400 resize-none">{{ old('notes', $tenant->notes) }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('manager.tenants.show', $tenant) }}" class="text-sm border border-border text-secondary hover:bg-surface px-5 py-2.5 rounded-xl transition-colors font-medium">Cancel</a>
            <button type="submit" class="text-sm bg-brand-600 hover:bg-brand-500 text-white px-6 py-2.5 rounded-xl font-semibold transition-colors">Save Changes</button>
        </div>
    </form>
</div>
@endsection
