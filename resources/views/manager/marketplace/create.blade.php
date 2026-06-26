@extends('layouts.app')

@section('title', 'Add Service Provider')
@section('page-title', 'Add Service Provider')
@section('page-subtitle', 'Add a new service to the tenant marketplace')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl space-y-6">

    <a href="{{ route('manager.marketplace.index') }}" class="inline-flex items-center gap-1.5 text-sm text-muted hover:text-primary transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Marketplace
    </a>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('manager.marketplace.store') }}" enctype="multipart/form-data"
          class="bg-card rounded-2xl border border-border shadow-sm p-6 space-y-5">
        @csrf

        {{-- Basic info --}}
        <div class="grid sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-primary mb-1">Provider Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                       placeholder="e.g. CleanFast Laundry">
            </div>

            <div>
                <label class="block text-sm font-medium text-primary mb-1">Category <span class="text-red-500">*</span></label>
                <select name="category" required
                        class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">Select category…</option>
                    @foreach($categories as $key => $meta)
                        <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>
                            {{ $meta['icon'] }} {{ $meta['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-primary mb-1">Phone <span class="text-red-500">*</span></label>
                <input type="text" name="phone" value="{{ old('phone') }}" required
                       class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                       placeholder="07xx xxx xxx">
            </div>

            <div>
                <label class="block text-sm font-medium text-primary mb-1">WhatsApp Number</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp') }}"
                       class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                       placeholder="Leave blank to use phone number">
            </div>

            <div>
                <label class="block text-sm font-medium text-primary mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-primary mb-1">Description <span class="text-red-500">*</span></label>
            <textarea name="description" rows="3" required
                      class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"
                      placeholder="Describe what this provider offers…">{{ old('description') }}</textarea>
        </div>

        {{-- Pricing --}}
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-primary mb-1">Price Label</label>
                <input type="text" name="price_label" value="{{ old('price_label') }}"
                       class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                       placeholder="e.g. from KES 300/load">
            </div>
            <div>
                <label class="block text-sm font-medium text-primary mb-1">Base Price (KES)</label>
                <input type="number" name="base_price" value="{{ old('base_price') }}" step="0.01" min="0"
                       class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
        </div>

        {{-- Logo --}}
        <div>
            <label class="block text-sm font-medium text-primary mb-1">Logo / Photo</label>
            <input type="file" name="logo" accept="image/*"
                   class="w-full text-sm text-muted file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
        </div>

        {{-- Toggles --}}
        <div class="grid sm:grid-cols-3 gap-4">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                       class="w-4 h-4 accent-brand-600">
                <span class="text-sm font-medium text-primary">Active (visible to tenants)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                       class="w-4 h-4 accent-brand-600">
                <span class="text-sm font-medium text-primary">Featured (shown first)</span>
            </label>
            <div>
                <label class="block text-sm font-medium text-primary mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                       class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
        </div>

        <div class="pt-2">
            <button type="submit"
                    class="bg-brand-600 hover:bg-brand-500 text-white font-semibold px-6 py-3 rounded-xl transition-colors text-sm">
                Add to Marketplace
            </button>
        </div>
    </form>
</div>
@endsection
