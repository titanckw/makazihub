@extends('layouts.app')

@section('title', 'Marketplace Bookings')
@section('page-title', 'Marketplace Bookings')
@section('page-subtitle', 'Tenant service requests across all providers')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-primary">All Bookings</h2>
            <p class="text-sm text-muted mt-1">{{ $bookings->total() }} booking request(s)</p>
        </div>
        <a href="{{ route('manager.marketplace.index') }}"
           class="border border-border rounded-lg px-4 py-2.5 text-sm font-medium text-primary hover:bg-white/50 transition-colors">
            ← Back to Providers
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm font-medium flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @forelse($bookings as $booking)
    <div class="bg-card rounded-2xl border border-border shadow-sm p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            {{-- Tenant info --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr($booking->tenant->user->name ?? 'T', 0, 1)) }}
                </div>
                <div>
                    <p class="font-bold text-primary text-sm">{{ $booking->tenant->user->name ?? 'Unknown Tenant' }}</p>
                    <p class="text-xs text-muted">{{ $booking->reference }} • {{ $booking->created_at->format('d M Y, g:i A') }}</p>
                </div>
            </div>

            {{-- Status badge + update form --}}
            <form method="POST" action="{{ route('manager.marketplace.bookings.update', $booking) }}"
                  class="flex items-center gap-2">
                @csrf @method('PATCH')
                <select name="status" onchange="this.form.submit()"
                        class="rounded-lg border border-border bg-white text-sm px-2.5 py-1.5 text-primary focus:outline-none focus:ring-2 focus:ring-brand-500">
                    @foreach($statuses as $key => $meta)
                        <option value="{{ $key }}" {{ $booking->status === $key ? 'selected' : '' }}>
                            {{ $meta['label'] }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="mt-4 grid sm:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-muted text-xs uppercase tracking-wide font-semibold mb-0.5">Service</p>
                <p class="font-medium text-primary">
                    {{ $booking->serviceProvider->category_icon }} {{ $booking->serviceProvider->name }}
                </p>
            </div>
            <div>
                <p class="text-muted text-xs uppercase tracking-wide font-semibold mb-0.5">Contact Phone</p>
                <p class="font-medium text-primary">{{ $booking->contact_phone ?? '–' }}</p>
            </div>
            @if($booking->preferred_date)
            <div>
                <p class="text-muted text-xs uppercase tracking-wide font-semibold mb-0.5">Preferred Date</p>
                <p class="font-medium text-primary">{{ $booking->preferred_date->format('d M Y, g:i A') }}</p>
            </div>
            @endif
        </div>

        @if($booking->notes)
        <div class="mt-3 pt-3 border-t border-border">
            <p class="text-xs text-muted font-semibold uppercase tracking-wide mb-1">Notes</p>
            <p class="text-sm text-muted">{{ $booking->notes }}</p>
        </div>
        @endif
    </div>
    @empty
    <div class="bg-card rounded-2xl border border-border shadow-sm p-12 text-center">
        <div class="text-5xl mb-4">📋</div>
        <h3 class="text-lg font-semibold text-primary mb-2">No Bookings Yet</h3>
        <p class="text-muted">Tenant booking requests will appear here.</p>
    </div>
    @endforelse

    @if($bookings->hasPages())
        <div class="mt-6">{{ $bookings->links() }}</div>
    @endif

</div>
@endsection
