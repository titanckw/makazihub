@extends('layouts.app')

@section('title', $service->name)
@section('page-title', $service->name)
@section('page-subtitle', $service->category_label)

@section('sidebar-nav')
    @include('tenant.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6 max-w-3xl">

    {{-- Back link --}}
    <a href="{{ route('tenant.marketplace.index') }}" class="inline-flex items-center gap-1.5 text-sm text-muted hover:text-primary transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Marketplace
    </a>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 flex items-start gap-3">
        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Provider card --}}
    <div class="bg-card rounded-2xl border border-border shadow-sm p-6">
        <div class="flex items-start gap-5">
            <div class="w-20 h-20 rounded-2xl bg-brand-50 flex items-center justify-center text-4xl shrink-0 overflow-hidden">
                @if($service->logo)
                    <img src="{{ asset('storage/' . $service->logo) }}" alt="{{ $service->name }}" class="w-full h-full object-cover">
                @else
                    {{ $service->category_icon }}
                @endif
            </div>

            <div class="flex-1">
                <div class="flex items-start justify-between gap-2 flex-wrap">
                    <div>
                        <h2 class="text-xl font-bold text-primary flex items-center gap-2 flex-wrap">
                            {{ $service->name }}
                            @if($service->is_featured)
                                <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide">Featured</span>
                            @endif
                        </h2>
                        <p class="text-sm text-muted mt-0.5">{{ $service->category_icon }} {{ $service->category_label }}</p>
                    </div>
                    @if($service->price_label)
                        <p class="text-lg font-bold text-brand-600">{{ $service->price_label }}</p>
                    @endif
                </div>
                <p class="text-muted mt-3 leading-relaxed">{{ $service->description }}</p>
            </div>
        </div>

        {{-- Contact row --}}
        <div class="mt-6 pt-6 border-t border-border flex flex-wrap gap-3">
            @if($service->whatsapp_url)
            <a href="{{ $service->whatsapp_url }}" target="_blank"
               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white font-semibold px-4 py-2.5 rounded-xl text-sm transition-colors">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/>
                </svg>
                Chat on WhatsApp
            </a>
            @endif

            <a href="{{ $service->call_url }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold px-4 py-2.5 rounded-xl text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                Call {{ $service->phone }}
            </a>

            @if($service->email)
            <a href="mailto:{{ $service->email }}"
               class="inline-flex items-center gap-2 border border-border rounded-xl px-4 py-2.5 text-sm font-medium text-primary hover:bg-white/50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Email
            </a>
            @endif

            @if($service->website)
            <a href="{{ $service->website }}" target="_blank"
               class="inline-flex items-center gap-2 border border-border rounded-xl px-4 py-2.5 text-sm font-medium text-primary hover:bg-white/50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Website
            </a>
            @endif
        </div>

        {{-- Working hours --}}
        @if($service->working_hours && count($service->working_hours))
        <div class="mt-4 pt-4 border-t border-border">
            <p class="text-xs font-semibold text-muted uppercase tracking-wider mb-2">Working Hours</p>
            <div class="flex flex-wrap gap-x-6 gap-y-1">
                @foreach($service->working_hours as $day => $hours)
                <span class="text-sm text-muted"><span class="font-medium text-primary capitalize">{{ $day }}</span>: {{ $hours }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Booking request form --}}
    <div class="bg-card rounded-2xl border border-border shadow-sm p-6">
        <h3 class="text-base font-bold text-primary mb-4">📅 Request a Booking</h3>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-4 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('tenant.marketplace.book', $service) }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-primary mb-1">Your Contact Phone <span class="text-red-500">*</span></label>
                <input type="tel" name="contact_phone" value="{{ old('contact_phone', auth()->user()->phone ?? '') }}"
                       required
                       class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                       placeholder="07xx xxx xxx">
                <p class="text-xs text-muted mt-1">The service provider will reach you on this number.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-primary mb-1">Preferred Date & Time <span class="text-muted font-normal">(optional)</span></label>
                <input type="datetime-local" name="preferred_date" value="{{ old('preferred_date') }}"
                       min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                       class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-primary mb-1">Additional Notes <span class="text-muted font-normal">(optional)</span></label>
                <textarea name="notes" rows="3"
                          class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"
                          placeholder="E.g. number of loads, delivery address, special instructions…">{{ old('notes') }}</textarea>
            </div>

            <button type="submit"
                    class="w-full bg-brand-600 hover:bg-brand-500 text-white font-semibold py-3 rounded-xl transition-colors text-sm">
                Send Booking Request
            </button>
        </form>
    </div>

    {{-- Past bookings with this provider --}}
    @if($myBookings->isNotEmpty())
    <div class="bg-card rounded-2xl border border-border shadow-sm p-6">
        <h3 class="text-base font-bold text-primary mb-4">My Previous Bookings</h3>
        <div class="space-y-3">
            @foreach($myBookings as $booking)
            <div class="flex items-center justify-between py-2 border-b border-border last:border-0">
                <div>
                    <p class="text-sm font-medium text-primary">{{ $booking->reference }}</p>
                    <p class="text-xs text-muted">{{ $booking->created_at->format('d M Y, g:i A') }}</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $booking->status_badge }}">
                    {{ $booking->status_label }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
