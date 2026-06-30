@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('page-subtitle', $user->name)

@section('sidebar-nav')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('superadmin.users.update', $user) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-border p-6 space-y-5">
            <h2 class="text-sm font-semibold text-primary">Account Details ({{ $user->role_label }})</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400
                               @error('name') border-danger @enderror">
                    @error('name')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-secondary mb-1.5">Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required
                        class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400
                               @error('phone') border-danger @enderror">
                    @error('phone')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-secondary mb-1.5">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400
                           @error('email') border-danger @enderror">
                @error('email')<p class="text-danger text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        @if($user->hasRole('staff') || $user->hasRole('tenant'))
        <div class="bg-white rounded-2xl border border-border p-6 space-y-5">
            <h2 class="text-sm font-semibold text-primary">Reporting Manager</h2>
            <select name="manager_id"
                class="w-full text-sm border border-border rounded-xl px-3 py-2 bg-surface focus:outline-none focus:ring-2 focus:ring-brand-400">
                <option value="">Select manager...</option>
                @foreach($managers as $manager)
                    <option value="{{ $manager->id }}" {{ old('manager_id', $profile->manager_id ?? '') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            <button type="submit"
                class="bg-brand-600 hover:bg-brand-500 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                Save Changes
            </button>
            <a href="{{ route('superadmin.users.show', $user) }}"
                class="text-sm border border-border text-secondary hover:bg-surface px-5 py-2.5 rounded-xl transition-colors">
                Cancel
            </a>
        </div>
    </form>

    @unless($user->hasRole('superadmin'))
    <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}"
          onsubmit="return confirm('Delete this user account permanently?');" class="mt-4">
        @csrf @method('DELETE')
        <button type="submit" class="text-sm text-danger hover:underline">Delete this user</button>
    </form>
    @endunless
</div>
@endsection
