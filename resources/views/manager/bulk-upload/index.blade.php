@extends('layouts.app')

@section('title', 'Bulk Upload')
@section('page-title', 'Bulk Upload')
@section('page-subtitle', 'Import tenants and properties in bulk using a CSV or Excel file')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl px-4 py-3">
            <svg class="w-5 h-5 mt-0.5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('warning'))
        <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-800 text-sm rounded-2xl px-4 py-3">
            <svg class="w-5 h-5 mt-0.5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    {{-- Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Tenants card --}}
        <div class="bg-white rounded-2xl border border-border p-6 flex flex-col gap-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-primary">Bulk Import Tenants</h2>
                    <p class="text-sm text-muted">Upload a CSV or Excel file with tenant details</p>
                </div>
            </div>

            <ul class="text-sm text-secondary space-y-1.5">
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Creates user accounts with default password <code class="bg-surface px-1.5 py-0.5 rounded text-xs font-mono">Tenant@1234</code>
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Skips duplicate emails and ID numbers automatically
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Supports CSV and Excel (.xlsx, .xls) formats
                </li>
            </ul>

            <div class="flex gap-3 mt-auto pt-2">
                <a href="{{ route('manager.bulk-upload.tenants.template') }}"
                   class="inline-flex items-center gap-2 text-sm border border-border text-secondary hover:bg-surface px-4 py-2 rounded-xl transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Template
                </a>
                <a href="{{ route('manager.bulk-upload.tenants') }}"
                   class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/>
                    </svg>
                    Upload Tenants
                </a>
            </div>
        </div>

        {{-- Properties card --}}
        <div class="bg-white rounded-2xl border border-border p-6 flex flex-col gap-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-navy-50 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-primary">Bulk Import Properties & Units</h2>
                    <p class="text-sm text-muted">Upload properties with their units in one file</p>
                </div>
            </div>

            <ul class="text-sm text-secondary space-y-1.5">
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    One row per unit — property fields repeat across rows
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Re-running won't duplicate existing properties
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    All new units default to <span class="font-medium text-blue-600">Vacant</span> status
                </li>
            </ul>

            <div class="flex gap-3 mt-auto pt-2">
                <a href="{{ route('manager.bulk-upload.properties.template') }}"
                   class="inline-flex items-center gap-2 text-sm border border-border text-secondary hover:bg-surface px-4 py-2 rounded-xl transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Template
                </a>
                <a href="{{ route('manager.bulk-upload.properties') }}"
                   class="inline-flex items-center gap-2 bg-navy-600 hover:bg-navy-500 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/>
                    </svg>
                    Upload Properties
                </a>
            </div>
        </div>
    </div>

    {{-- Tips --}}
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
        <h3 class="text-sm font-semibold text-amber-800 mb-2">Before you upload</h3>
        <ul class="text-sm text-amber-700 space-y-1.5 list-disc list-inside">
            <li>Download and use the provided template — column headers must match exactly.</li>
            <li>Remove the example row from the template before uploading your real data.</li>
            <li>Maximum file size: <strong>5 MB</strong>. Supported formats: <strong>.csv, .xlsx, .xls</strong>.</li>
            <li>Rows with missing required fields or duplicates are skipped and reported after import.</li>
        </ul>
    </div>

</div>
@endsection
