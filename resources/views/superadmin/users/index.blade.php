@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'Users')
@section('page-subtitle', 'Manage managers, staff and tenants across the platform')

@section('sidebar-nav')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-muted">{{ $users->total() }} user{{ $users->total() !== 1 ? 's' : '' }} found</p>
        <a href="{{ route('superadmin.users.create') }}"
           class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add User
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-border p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-semibold text-secondary mb-1.5">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Name, email or phone…"
                    class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-semibold text-secondary mb-1.5">Role</label>
                <select name="role" class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                    <option value="">All Roles</option>
                    @foreach(['superadmin', 'manager', 'staff', 'tenant'] as $r)
                        <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs font-semibold text-secondary mb-1.5">Status</label>
                <select name="status" class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="text-sm bg-navy-500 hover:bg-navy-400 text-white px-4 py-2 rounded-xl font-medium transition-colors">Filter</button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('superadmin.users.index') }}" class="text-sm border border-border text-secondary hover:bg-surface px-4 py-2 rounded-xl transition-colors">Clear</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        @if($users->isEmpty())
            <div class="py-16 text-center">
                <p class="text-muted text-sm">No users found.
                    <a href="{{ route('superadmin.users.create') }}" class="text-brand-600 font-semibold hover:underline">Add the first user</a>.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface border-b border-border">
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">User</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Role</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Phone</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Status</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Joined</th>
                            <th class="text-right px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($users as $u)
                        <tr class="hover:bg-surface/60 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-navy-500 flex items-center justify-center shrink-0">
                                        <span class="text-sm font-bold text-white">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-primary">{{ $u->name }}</p>
                                        <p class="text-xs text-muted">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-secondary">{{ $u->role_label }}</td>
                            <td class="px-6 py-4 text-secondary text-xs">{{ $u->phone }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $u->is_active ? 'bg-success-bg text-success' : 'bg-danger-bg text-danger' }}">
                                    {{ $u->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-secondary text-xs">{{ $u->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('superadmin.users.show', $u) }}"
                                       class="text-xs bg-navy-500 hover:bg-navy-400 text-white px-3 py-1.5 rounded-lg font-medium transition-colors">View</a>
                                    <a href="{{ route('superadmin.users.edit', $u) }}"
                                       class="text-xs border border-border text-secondary hover:bg-surface px-3 py-1.5 rounded-lg font-medium transition-colors">Edit</a>
                                    <form method="POST" action="{{ route('superadmin.users.toggle-status', $u) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-xs border border-border text-secondary hover:bg-surface px-3 py-1.5 rounded-lg font-medium transition-colors">
                                            {{ $u->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
            <div class="px-6 py-4 border-t border-border">{{ $users->links() }}</div>
            @endif
        @endif
    </div>

</div>
@endsection
