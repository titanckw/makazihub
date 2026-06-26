@extends('layouts.app')

@section('title', 'My Service Bookings')
@section('page-title', 'My Bookings')
@section('page-subtitle', 'Your marketplace service requests')

@section('sidebar-nav')
    @include('tenant.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-primary">Booking History</h2>
            <p class="text-sm text-muted mt-1">Track all your service requests</p>
        </div>
        <a href="{{ route('tenant.marketplace.index') }}"
           class="bg-brand-600 hover:bg-brand-500 text-white font-semibold px-4 py-2.5 rounded-lg transition-colors text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Browse Services
        </a>
    </div>

    @forelse($bookings as $booking)
    <div class="bg-card rounded-2xl border border-border shadow-sm p-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center text-2xl shrink-0">
                    {{ $booking->serviceProvider->category_icon }}
                </div>
                <div>
                    <p class="font-bold text-primary">{{ $booking->serviceProvider->name }}</p>
                    <p class="text-xs text-muted">{{ $booking->reference }} • {{ $booking->created_at->format('d M Y') }}</p>
                </div>
            </div>
            <span class="text-sm font-semibold px-3 py-1.5 rounded-full {{ $booking->status_badge }}">
                {{ $booking->status_label }}
            </span>
        </div>

        @if($booking->preferred_date || $booking->notes)
        <div class="mt-4 pt-4 border-t border-border grid sm:grid-cols-2 gap-3 text-sm">
            @if($booking->preferred_date)
            <div>
                <p class="text-muted text-xs uppercase tracking-wide font-semibold mb-0.5">Preferred Date</p>
                <p class="font-medium text-primary">{{ $booking->preferred_date->format('d M Y, g:i A') }}</p>
            </div>
            @endif
            @if($booking->notes)
            <div>
                <p class="text-muted text-xs uppercase tracking-wide font-semibold mb-0.5">Notes</p>
                <p class="text-muted">{{ $booking->notes }}</p>
            </div>
            @endif
        </div>
        @endif

        <div class="mt-3 flex items-center gap-3">
            @if($booking->serviceProvider->whatsapp_url)
            <a href="{{ $booking->serviceProvider->whatsapp_url }}" target="_blank"
               class="text-xs font-medium text-green-700 hover:underline flex items-center gap-1">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/>
                </svg>
                Follow up on WhatsApp
            </a>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-card rounded-2xl border border-border shadow-sm p-12 text-center">
        <div class="text-5xl mb-4">📋</div>
        <h3 class="text-lg font-semibold text-primary mb-2">No Bookings Yet</h3>
        <p class="text-muted mb-6">You haven't made any service bookings. Browse the marketplace to get started.</p>
        <a href="{{ route('tenant.marketplace.index') }}"
           class="inline-block bg-brand-600 hover:bg-brand-500 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
            Browse Marketplace
        </a>
    </div>
    @endforelse

    @if($bookings->hasPages())
        <div class="mt-6">{{ $bookings->links() }}</div>
    @endif

</div>
@endsection
