@extends('layouts.app')

@section('title', 'Marketplace')
@section('page-title', 'Marketplace')
@section('page-subtitle', 'Trusted services for residents of your property')

@section('sidebar-nav')
    @include('tenant.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header row --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-lg font-semibold text-primary">Available Services</h2>
            <p class="text-sm text-muted mt-1">Browse and request services from our partner providers</p>
        </div>
        <a href="{{ route('tenant.marketplace.my-bookings') }}"
           class="inline-flex items-center gap-2 border border-border rounded-lg px-4 py-2 text-sm font-medium text-primary hover:bg-white/50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            My Bookings
        </a>
    </div>

    {{-- Search + Category filters --}}
    <form method="GET" action="{{ route('tenant.marketplace.index') }}" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search services…"
                   class="w-full pl-9 pr-4 py-2 rounded-lg border border-border bg-card text-sm text-primary placeholder:text-muted focus:outline-none focus:ring-2 focus:ring-brand-500">
        </div>
        <select name="category" onchange="this.form.submit()"
                class="rounded-lg border border-border bg-card text-sm text-primary px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500">
            <option value="">All Categories</option>
            @foreach($categories as $key => $meta)
                <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>
                    {{ $meta['icon'] }} {{ $meta['label'] }}
                    @if($categoryCounts->get($key))
                        ({{ $categoryCounts->get($key) }})
                    @endif
                </option>
            @endforeach
        </select>
        @if(request('search') || request('category'))
            <a href="{{ route('tenant.marketplace.index') }}"
               class="text-sm text-muted hover:text-primary px-3 py-2 flex items-center">Clear</a>
        @endif
    </form>

    {{-- Category pills (visual shortcut) --}}
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('tenant.marketplace.index') }}"
           class="px-3 py-1.5 rounded-full text-xs font-semibold border transition-colors
                  {{ !request('category') ? 'bg-brand-600 text-white border-brand-600' : 'border-border text-muted hover:border-brand-400 hover:text-primary' }}">
            All
        </a>
        @foreach($categories as $key => $meta)
            @if($categoryCounts->get($key, 0) > 0)
            <a href="{{ route('tenant.marketplace.index', ['category' => $key]) }}"
               class="px-3 py-1.5 rounded-full text-xs font-semibold border transition-colors
                      {{ request('category') === $key ? 'bg-brand-600 text-white border-brand-600' : 'border-border text-muted hover:border-brand-400 hover:text-primary' }}">
                {{ $meta['icon'] }} {{ $meta['label'] }}
            </a>
            @endif
        @endforeach
    </div>

    {{-- Service cards --}}
    @forelse($services as $service)
    <div class="bg-card rounded-2xl border border-border shadow-sm p-6 hover:shadow-md transition-shadow">
        <div class="flex items-start gap-4">
            {{-- Logo / Icon --}}
            <div class="w-14 h-14 rounded-xl bg-brand-50 flex items-center justify-center text-2xl shrink-0 overflow-hidden">
                @if($service->logo)
                    <img src="{{ asset('storage/' . $service->logo) }}" alt="{{ $service->name }}" class="w-full h-full object-cover">
                @else
                    {{ $service->category_icon }}
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2 flex-wrap">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-base font-bold text-primary">{{ $service->name }}</h3>
                            @if($service->is_featured)
                                <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wide">Featured</span>
                            @endif
                        </div>
                        <span class="inline-flex items-center gap-1 text-xs text-muted mt-0.5">
                            <span>{{ $service->category_icon }}</span>
                            {{ $service->category_label }}
                        </span>
                    </div>
                    @if($service->price_label)
                        <span class="text-sm font-semibold text-brand-600 shrink-0">{{ $service->price_label }}</span>
                    @endif
                </div>

                <p class="text-sm text-muted mt-2 leading-relaxed">{{ Str::limit($service->description, 120) }}</p>

                <div class="flex items-center justify-between mt-4 pt-4 border-t border-border">
                    <div class="flex items-center gap-3 flex-wrap">
                        {{-- WhatsApp --}}
                        @if($service->whatsapp_url)
                        <a href="{{ $service->whatsapp_url }}" target="_blank"
                           class="inline-flex items-center gap-1.5 text-xs font-medium text-green-700 bg-green-50 px-2.5 py-1 rounded-lg hover:bg-green-100 transition-colors">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/>
                            </svg>
                            WhatsApp
                        </a>
                        @endif

                        {{-- Call --}}
                        <a href="{{ $service->call_url }}"
                           class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg hover:bg-blue-100 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ $service->phone }}
                        </a>
                    </div>

                    <a href="{{ route('tenant.marketplace.show', $service) }}"
                       class="text-brand-600 hover:text-brand-500 font-semibold text-sm">
                        View & Book →
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-card rounded-2xl border border-border shadow-sm p-12 text-center">
        <div class="text-5xl mb-4">🛒</div>
        <h3 class="text-lg font-semibold text-primary mb-2">No Services Found</h3>
        <p class="text-muted">
            @if(request('search') || request('category'))
                No services match your filter. <a href="{{ route('tenant.marketplace.index') }}" class="text-brand-600 hover:underline">Clear filters</a>
            @else
                Your property manager hasn't added any services yet. Check back soon!
            @endif
        </p>
    </div>
    @endforelse

</div>
@endsection
