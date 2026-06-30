@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('page-subtitle', 'Manage your personal details and avatar')

@section('sidebar-nav')
    @include('staff.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6 max-w-2xl">

    @if(session('success'))
        <div class="bg-success-bg text-success text-sm font-medium px-4 py-3 rounded-xl">{{ session('success') }}</div>
    @endif

    {{-- Avatar --}}
    <div class="bg-white rounded-2xl border border-border p-6">
        <h3 class="font-semibold text-primary mb-4">Profile Photo</h3>
        <div class="flex items-center gap-5">
            <img src="{{ $user->avatar_url }}" class="w-16 h-16 rounded-full object-cover border border-border" alt="Avatar">
            <form method="POST" action="{{ route('staff.profile.avatar') }}" enctype="multipart/form-data" class="flex items-center gap-3">
                @csrf
                <input type="file" name="avatar" accept="image/*" required
                    class="text-sm text-secondary file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-navy-100 file:text-primary file:text-sm file:font-medium">
                <button type="submit" class="px-4 py-2 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-500 transition-colors">
                    Upload
                </button>
            </form>
        </div>
        @error('avatar')<p class="text-danger text-xs mt-2">{{ $message }}</p>@enderror
    </div>

    {{-- Details --}}
    <div class="bg-white rounded-2xl border border-border p-6">
        <h3 class="font-semibold text-primary mb-4">Personal Details</h3>
        <form method="POST" action="{{ route('staff.profile.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="text-xs font-semibold text-secondary uppercase tracking-wider">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="mt-1 w-full px-3 py-2 border border-border rounded-xl text-sm" required>
            </div>
            <div>
                <label class="text-xs font-semibold text-secondary uppercase tracking-wider">Email</label>
                <input type="email" value="{{ $user->email }}" disabled
                    class="mt-1 w-full px-3 py-2 border border-border rounded-xl text-sm bg-surface text-muted">
                <p class="text-xs text-muted mt-1">Contact your manager to change your email.</p>
            </div>
            <div>
                <label class="text-xs font-semibold text-secondary uppercase tracking-wider">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                    class="mt-1 w-full px-3 py-2 border border-border rounded-xl text-sm">
            </div>
            <div>
                <label class="text-xs font-semibold text-secondary uppercase tracking-wider">Bio</label>
                <textarea name="bio" rows="3" class="mt-1 w-full px-3 py-2 border border-border rounded-xl text-sm">{{ old('bio', $user->bio) }}</textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-500 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- Employment info (read-only) --}}
    <div class="bg-white rounded-2xl border border-border p-6">
        <h3 class="font-semibold text-primary mb-4">Employment Info</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-muted text-xs">Role</dt><dd class="text-primary font-medium">{{ ucfirst($staff->role) }}</dd></div>
            <div><dt class="text-muted text-xs">Department</dt><dd class="text-primary font-medium">{{ $staff->department ?? '—' }}</dd></div>
            <div><dt class="text-muted text-xs">Employment Type</dt><dd class="text-primary font-medium">{{ $staff->employment_type_label }}</dd></div>
            <div><dt class="text-muted text-xs">Manager</dt><dd class="text-primary font-medium">{{ $staff->manager->name }}</dd></div>
        </dl>
    </div>
</div>
@endsection
