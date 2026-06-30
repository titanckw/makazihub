@extends('layouts.app')

@section('title', 'Staff Documents')
@section('page-title', 'Staff Documents')
@section('page-subtitle', 'Securely store CVs, IDs and contracts — visible only to managers')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6" x-data="{ showForm: false }">

    @if(session('success'))
        <div class="bg-success-bg text-success text-sm font-medium px-4 py-3 rounded-xl">{{ session('success') }}</div>
    @endif

    <div class="flex justify-between items-center">
        <form method="GET" class="flex gap-2">
            <select name="staff_id" onchange="this.form.submit()" class="px-3 py-2 border border-border rounded-xl text-sm">
                <option value="">All Staff</option>
                @foreach($staffList as $s)
                    <option value="{{ $s->id }}" {{ request('staff_id') == $s->id ? 'selected' : '' }}>{{ $s->user->name }}</option>
                @endforeach
            </select>
            <select name="category" onchange="this.form.submit()" class="px-3 py-2 border border-border rounded-xl text-sm">
                <option value="">All Categories</option>
                <option value="cv" {{ request('category') === 'cv' ? 'selected' : '' }}>CV / Resume</option>
                <option value="id" {{ request('category') === 'id' ? 'selected' : '' }}>ID Document</option>
                <option value="contract" {{ request('category') === 'contract' ? 'selected' : '' }}>Contract</option>
                <option value="certificate" {{ request('category') === 'certificate' ? 'selected' : '' }}>Certificate</option>
                <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </form>
        <button @click="showForm = !showForm" class="px-5 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-500 transition-colors">
            + Upload Document
        </button>
    </div>

    <div x-show="showForm" x-transition class="bg-white rounded-2xl border border-border p-6">
        <form method="POST" action="{{ route('manager.documents.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="text-xs font-semibold text-secondary uppercase tracking-wider">Staff Member</label>
                <select name="staff_id" class="mt-1 w-full px-3 py-2 border border-border rounded-xl text-sm" required>
                    <option value="">Select staff…</option>
                    @foreach($staffList as $s)
                        <option value="{{ $s->id }}">{{ $s->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-secondary uppercase tracking-wider">Category</label>
                <select name="category" class="mt-1 w-full px-3 py-2 border border-border rounded-xl text-sm" required>
                    <option value="cv">CV / Resume</option>
                    <option value="id">ID Document</option>
                    <option value="contract">Contract</option>
                    <option value="certificate">Certificate</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="text-xs font-semibold text-secondary uppercase tracking-wider">Title</label>
                <input type="text" name="title" placeholder="e.g. National ID — front page" class="mt-1 w-full px-3 py-2 border border-border rounded-xl text-sm" required>
            </div>
            <div class="sm:col-span-2">
                <label class="text-xs font-semibold text-secondary uppercase tracking-wider">File (PDF, DOC, JPG, PNG — max 10MB)</label>
                <input type="file" name="file" required
                    class="mt-1 text-sm text-secondary file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-navy-100 file:text-primary file:text-sm file:font-medium">
            </div>
            <div class="sm:col-span-2 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-500 transition-colors">
                    Upload
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-surface text-secondary text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">Staff</th>
                    <th class="px-6 py-3 text-left">Title</th>
                    <th class="px-6 py-3 text-left">Category</th>
                    <th class="px-6 py-3 text-left">Size</th>
                    <th class="px-6 py-3 text-left">Uploaded</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($documents as $doc)
                    <tr>
                        <td class="px-6 py-3 text-primary font-medium">{{ $doc->staff->user->name }}</td>
                        <td class="px-6 py-3 text-secondary">{{ $doc->title }}</td>
                        <td class="px-6 py-3 text-secondary">{{ $doc->category_label }}</td>
                        <td class="px-6 py-3 text-secondary">{{ $doc->formatted_size }}</td>
                        <td class="px-6 py-3 text-secondary">{{ $doc->created_at->diffForHumans() }}</td>
                        <td class="px-6 py-3 text-right space-x-3">
                            <a href="{{ route('manager.documents.download', $doc) }}" class="text-brand-600 text-xs font-semibold hover:underline">Download</a>
                            <form method="POST" action="{{ route('manager.documents.destroy', $doc) }}" class="inline" onsubmit="return confirm('Delete this document?')">
                                @csrf @method('DELETE')
                                <button class="text-danger text-xs font-semibold hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-muted">No documents uploaded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $documents->links() }}</div>
    </div>
</div>
@endsection
