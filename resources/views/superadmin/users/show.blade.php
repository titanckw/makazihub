@extends('layouts.app')

@section('title', $user->name)
@section('page-title', $user->name)
@section('page-subtitle', $user->role_label)

@section('sidebar-nav')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl space-y-6">

    <div class="bg-white rounded-2xl border border-border p-6 space-y-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-navy-500 flex items-center justify-center shrink-0">
                <span class="text-xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            </div>
            <div>
                <p class="text-lg font-semibold text-primary">{{ $user->name }}</p>
                <p class="text-sm text-muted">{{ $user->email }}</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold mt-1 {{ $user->is_active ? 'bg-success-bg text-success' : 'bg-danger-bg text-danger' }}">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-border text-sm">
            <div>
                <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-1">Role</p>
                <p class="text-primary">{{ $user->role_label }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-1">Phone</p>
                <p class="text-primary">{{ $user->phone }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-1">Joined</p>
                <p class="text-primary">{{ $user->created_at->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    @if($profile && $user->hasRole('staff'))
    <div class="bg-white rounded-2xl border border-border p-6 space-y-3">
        <h2 class="text-sm font-semibold text-primary">Staff Profile</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><p class="text-xs text-secondary mb-1">Job Title</p><p class="text-primary">{{ $profile->role ?? '—' }}</p></div>
            <div><p class="text-xs text-secondary mb-1">Department</p><p class="text-primary">{{ $profile->department ?? '—' }}</p></div>
            <div><p class="text-xs text-secondary mb-1">Manager</p><p class="text-primary">{{ $profile->manager->name ?? '—' }}</p></div>
            <div><p class="text-xs text-secondary mb-1">Status</p><p class="text-primary">{{ ucfirst($profile->status) }}</p></div>
        </div>
    </div>
    @endif

    @if($profile && $user->hasRole('tenant'))
    <div class="bg-white rounded-2xl border border-border p-6 space-y-3">
        <h2 class="text-sm font-semibold text-primary">Tenant Profile</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><p class="text-xs text-secondary mb-1">ID Number</p><p class="text-primary">{{ $profile->id_number ?? '—' }}</p></div>
            <div><p class="text-xs text-secondary mb-1">Manager</p><p class="text-primary">{{ $profile->manager->name ?? '—' }}</p></div>
            <div><p class="text-xs text-secondary mb-1">Unit</p><p class="text-primary">{{ $profile->unit->unit_number ?? 'Not yet assigned' }}</p></div>
            <div><p class="text-xs text-secondary mb-1">Status</p><p class="text-primary">{{ ucfirst($profile->status) }}</p></div>
        </div>
        @if(!$profile->unit)
        <p class="text-xs text-warning">This tenant has no unit/lease assigned yet. Assign one from the manager's Tenants screen.</p>
        @endif
    </div>
    @endif

    <div class="flex items-center gap-3">
        <a href="{{ route('superadmin.users.edit', $user) }}"
            class="bg-brand-600 hover:bg-brand-500 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
            Edit User
        </a>
        <a href="{{ route('superadmin.users.index') }}"
            class="text-sm border border-border text-secondary hover:bg-surface px-5 py-2.5 rounded-xl transition-colors">
            Back to Users
        </a>
    </div>
</div>
@endsection
