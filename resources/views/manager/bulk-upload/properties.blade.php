@extends('layouts.app')

@section('title', 'Import Properties')
@section('page-title', 'Import Properties & Units')
@section('page-subtitle', 'Upload a CSV or Excel file to add properties and their units')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- Back --}}
    <a href="{{ route('manager.bulk-upload.index') }}"
       class="inline-flex items-center gap-2 text-sm text-secondary hover:text-primary transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Bulk Upload
    </a>

    {{-- Validation errors --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
            <p class="text-sm font-semibold text-red-700 mb-1">Please fix the following:</p>
            <ul class="text-sm text-red-600 list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Per-row import errors from session --}}
    @if(session('import_errors') && count(session('import_errors')) > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">
            <p class="text-sm font-semibold text-amber-800 mb-2">Rows skipped during import:</p>
            <ul class="text-sm text-amber-700 list-disc list-inside space-y-0.5 max-h-48 overflow-y-auto">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- How it works --}}
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-sm text-blue-800 space-y-1">
        <p class="font-semibold">How the file is structured</p>
        <p>Each <strong>row represents one unit</strong>. For a property with 10 units, you'll have 10 rows — all repeating the same property name, address, city, county and type. The importer groups rows by <code class="font-mono text-xs bg-blue-100 px-1 rounded">property_name</code> to create the property once and attach all its units.</p>
    </div>

    {{-- Upload card --}}
    <div class="bg-white rounded-2xl border border-border p-6 space-y-6">

        <div class="flex items-center justify-between">
            <h2 class="text-base font-semibold text-primary">Upload Property File</h2>
            <a href="{{ route('manager.bulk-upload.properties.template') }}"
               class="inline-flex items-center gap-1.5 text-xs text-brand-600 hover:underline font-semibold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download Template
            </a>
        </div>

        <form action="{{ route('manager.bulk-upload.properties.import') }}" method="POST" enctype="multipart/form-data" id="propertyUploadForm">
            @csrf

            {{-- Drop zone --}}
            <div id="dropZone"
                 class="border-2 border-dashed border-border rounded-2xl p-10 text-center cursor-pointer hover:border-brand-400 hover:bg-brand-50/30 transition-colors"
                 onclick="document.getElementById('fileInput').click()">
                <svg class="w-10 h-10 text-muted mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <p id="dropLabel" class="text-sm font-medium text-secondary">Drag and drop your file here, or <span class="text-brand-600">browse</span></p>
                <p class="text-xs text-muted mt-1">CSV, XLSX, XLS — max 5 MB</p>
                <input id="fileInput" type="file" name="file" accept=".csv,.xlsx,.xls" class="hidden">
            </div>

            <div id="filePreview" class="hidden mt-3 flex items-center gap-3 bg-surface rounded-xl px-4 py-3">
                <svg class="w-5 h-5 text-navy-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span id="fileName" class="text-sm text-primary font-medium truncate"></span>
                <button type="button" onclick="clearFile()" class="ml-auto text-muted hover:text-red-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <button type="submit"
                    class="mt-5 w-full inline-flex items-center justify-center gap-2 bg-navy-600 hover:bg-navy-500 text-white text-sm font-semibold px-4 py-3 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/>
                </svg>
                Import Properties & Units
            </button>
        </form>
    </div>

    {{-- Column reference --}}
    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <div class="px-6 py-4 border-b border-border">
            <h3 class="text-sm font-semibold text-primary">CSV Column Reference</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Column</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Required</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @php
                    $columns = [
                        ['property_name',  true,  'Groups rows into one property — must match exactly across rows'],
                        ['address',        true,  'Street address of the property'],
                        ['city',           true,  'e.g. Nairobi'],
                        ['county',         true,  'e.g. Nairobi, Kiambu, Mombasa'],
                        ['property_type',  true,  'apartment | maisonette | commercial | bedsitter | townhouse'],
                        ['description',    false, 'Brief description of the property'],
                        ['unit_number',    true,  'Unique unit ID, e.g. A01, 2B, GF-01'],
                        ['unit_type',      false, '1br | 2br | 3br | studio | bedsitter | commercial'],
                        ['floor',          false, 'Floor number (0 = ground)'],
                        ['rent_amount',    true,  'Monthly rent in KES — numbers only, no commas'],
                        ['deposit_amount', false, 'Deposit in KES. Defaults to 2× rent if blank'],
                    ];
                    @endphp
                    @foreach($columns as [$col, $req, $note])
                    <tr class="hover:bg-surface/60">
                        <td class="px-4 py-3 font-mono text-xs text-primary">{{ $col }}</td>
                        <td class="px-4 py-3">
                            @if($req)
                                <span class="inline-flex items-center bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">Required</span>
                            @else
                                <span class="text-muted text-xs">Optional</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-secondary">{{ $note }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const fileInput = document.getElementById('fileInput');
    const dropZone  = document.getElementById('dropZone');
    const preview   = document.getElementById('filePreview');
    const fileName  = document.getElementById('fileName');

    fileInput.addEventListener('change', () => showPreview(fileInput.files[0]));

    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('border-brand-400', 'bg-brand-50/30'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-brand-400', 'bg-brand-50/30'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('border-brand-400', 'bg-brand-50/30');
        const file = e.dataTransfer.files[0];
        if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            showPreview(file);
        }
    });

    function showPreview(file) {
        if (!file) return;
        fileName.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
        dropZone.classList.add('hidden');
        preview.classList.remove('hidden');
    }

    function clearFile() {
        fileInput.value = '';
        preview.classList.add('hidden');
        dropZone.classList.remove('hidden');
    }
</script>
@endpush
