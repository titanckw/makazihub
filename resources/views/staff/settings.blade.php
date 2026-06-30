@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')
@section('page-subtitle', 'Manage your account preferences')

@section('sidebar-nav')
    @include('staff.partials.sidebar')
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{ tab: 'profile' }">

    {{-- Tab Navigation --}}
    <div class="bg-card border border-border rounded-2xl p-1 flex gap-1">
        <button @click="tab = 'profile'"
            :class="tab === 'profile' ? 'bg-brand-600 text-white shadow-sm' : 'text-secondary hover:text-primary hover:bg-surface'"
            class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Profile
        </button>
        <button @click="tab = 'security'"
            :class="tab === 'security' ? 'bg-brand-600 text-white shadow-sm' : 'text-secondary hover:text-primary hover:bg-surface'"
            class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Security
        </button>
    </div>

    {{-- Profile Tab --}}
    <div x-show="tab === 'profile'" x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">

        <div class="bg-card border border-border rounded-2xl overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-5 border-b border-border flex items-center gap-4">
                <div class="w-14 h-14 bg-brand-600 rounded-2xl flex items-center justify-center shrink-0">
                    <span class="text-white text-xl font-700">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
                <div>
                    <p class="font-display font-700 text-primary text-lg leading-tight">{{ $user->name }}</p>
                    <span class="inline-flex items-center gap-1.5 mt-1 px-2.5 py-0.5 bg-brand-50 text-brand-700 text-xs font-semibold rounded-full">
                        <span class="w-1.5 h-1.5 bg-brand-600 rounded-full"></span>
                        {{ $user->role_label }}
                    </span>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('settings.profile') }}" class="px-6 py-6 space-y-5">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-primary mb-1.5">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full px-4 py-2.5 border border-border rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-all @error('name') border-danger @enderror"
                            placeholder="Your full name">
                        @error('name')
                            <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-primary mb-1.5">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full px-4 py-2.5 border border-border rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-all @error('email') border-danger @enderror"
                            placeholder="you@example.com">
                        @error('email')
                            <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-primary mb-1.5">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                            class="w-full px-4 py-2.5 border border-border rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-all @error('phone') border-danger @enderror"
                            placeholder="+254 7XX XXX XXX">
                        @error('phone')
                            <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-primary mb-1.5">Role</label>
                        <input type="text" value="{{ $user->role_label }}" disabled
                            class="w-full px-4 py-2.5 border border-border rounded-xl text-sm bg-surface text-muted cursor-not-allowed">
                        <p class="mt-1.5 text-xs text-muted">Role is managed by the system administrator.</p>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Security Tab --}}
    <div x-show="tab === 'security'" x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
        style="display:none">

        <div class="bg-card border border-border rounded-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-border">
                <h3 class="font-display font-700 text-primary">Change Password</h3>
                <p class="text-sm text-muted mt-0.5">Choose a strong password with at least 8 characters.</p>
            </div>

            <form method="POST" action="{{ route('settings.password') }}" class="px-6 py-6 space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-medium text-primary mb-1.5">Current Password</label>
                    <input type="password" name="current_password"
                        class="w-full px-4 py-2.5 border border-border rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-all @error('current_password') border-danger @enderror"
                        placeholder="Enter your current password">
                    @error('current_password')
                        <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-primary mb-1.5">New Password</label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2.5 border border-border rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-all @error('password') border-danger @enderror"
                        placeholder="At least 8 characters">
                    @error('password')
                        <p class="mt-1.5 text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-primary mb-1.5">Confirm New Password</label>
                    <input type="password" name="password_confirmation"
                        class="w-full px-4 py-2.5 border border-border rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-all"
                        placeholder="Repeat new password">
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        {{-- Account Info Card --}}
        <div class="mt-6 bg-card border border-border rounded-2xl px-6 py-5">
            <h3 class="font-display font-700 text-primary mb-4">Account Information</h3>
            <dl class="space-y-3">
                <div class="flex items-center justify-between py-2 border-b border-border/50">
                    <dt class="text-sm text-muted">Account Status</dt>
                    <dd>
                        @if($user->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-success-bg text-success text-xs font-semibold rounded-full">
                                <span class="w-1.5 h-1.5 bg-success rounded-full"></span> Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-danger-bg text-danger text-xs font-semibold rounded-full">
                                <span class="w-1.5 h-1.5 bg-danger rounded-full"></span> Inactive
                            </span>
                        @endif
                    </dd>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-border/50">
                    <dt class="text-sm text-muted">Member Since</dt>
                    <dd class="text-sm font-medium text-primary">{{ $user->created_at->format('d M Y') }}</dd>
                </div>
                <div class="flex items-center justify-between py-2">
                    <dt class="text-sm text-muted">Last Updated</dt>
                    <dd class="text-sm font-medium text-primary">{{ $user->updated_at->diffForHumans() }}</dd>
                </div>
            </dl>
        </div>
    </div>

</div>
@endsection
