@extends('layouts.app')

@section('title', 'Marketplace Services')
@section('page-title', 'Marketplace')
@section('page-subtitle', 'Manage service providers available to tenants')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-primary">Service Providers</h2>
            <p class="text-sm text-muted mt-1">{{ $services->total() }} provider(s) configured</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('manager.marketplace.bookings') }}"
               class="border border-border rounded-lg px-4 py-2.5 text-sm font-medium text-primary hover:bg-white/50 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Tenant Bookings
            </a>
            <a href="{{ route('manager.marketplace.create') }}"
               class="bg-brand-600 hover:bg-brand-500 text-white font-semibold px-4 py-2.5 rounded-lg transition-colors flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Provider
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 flex items-center gap-2 text-sm font-medium">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @forelse($services as $service)
    <div class="bg-card rounded-2xl border border-border shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center text-2xl shrink-0 overflow-hidden">
            @if($service->logo)
                <img src="{{ asset('storage/' . $service->logo) }}" class="w-full h-full object-cover">
            @else
                {{ $service->category_icon }}
            @endif
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <p class="font-bold text-primary">{{ $service->name }}</p>
                @if($service->is_featured)
                    <span class="bg-amber-100 text-amber-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full">Featured</span>
                @endif
                @if(!$service->is_active)
                    <span class="bg-gray-100 text-gray-500 text-[10px] font-bold px-1.5 py-0.5 rounded-full">Inactive</span>
                @endif
            </div>
            <p class="text-xs text-muted mt-0.5">{{ $service->category_icon }} {{ $service->category_label }} • {{ $service->phone }}</p>
            @if($service->price_label)
                <p class="text-xs text-brand-600 font-medium mt-0.5">{{ $service->price_label }}</p>
            @endif
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('manager.marketplace.edit', $service) }}"
               class="text-sm font-medium text-blue-600 hover:text-blue-500 border border-blue-200 px-3 py-1.5 rounded-lg hover:bg-blue-50 transition-colors">
                Edit
            </a>
            <form method="POST" action="{{ route('manager.marketplace.destroy', $service) }}"
                  onsubmit="return confirm('Remove {{ $service->name }} from the marketplace?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="text-sm font-medium text-red-600 hover:text-red-500 border border-red-200 px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                    Remove
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="bg-card rounded-2xl border border-border shadow-sm p-12 text-center">
        <div class="text-5xl mb-4">🛒</div>
        <h3 class="text-lg font-semibold text-primary mb-2">No Service Providers Yet</h3>
        <p class="text-muted mb-6">Add your first service provider so tenants can access laundry, gas delivery, mama fua, and more.</p>
        <a href="{{ route('manager.marketplace.create') }}"
           class="inline-block bg-brand-600 hover:bg-brand-500 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors">
            Add First Provider
        </a>
    </div>
    @endforelse

    @if($services->hasPages())
        <div class="mt-6">{{ $services->links() }}</div>
    @endif

</div>
@endsection
