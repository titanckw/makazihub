{{-- resources/views/manager/receipts/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Receipts')
@section('page-title', 'Receipts')
@section('page-subtitle', 'Download and send payment receipts')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-2xl border border-border overflow-hidden">
        @if($receipts->isEmpty())
            <div class="py-16 text-center">
                <svg class="w-12 h-12 text-muted mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-muted text-sm">No receipts yet. Receipts are generated automatically when payments are recorded.</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface border-b border-border">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Receipt #</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Tenant</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Invoice</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Amount</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Issued</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-secondary uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach($receipts as $receipt)
                    <tr class="hover:bg-surface/60 transition-colors">
                        <td class="px-6 py-4 font-mono text-xs font-semibold text-secondary">{{ $receipt->receipt_number }}</td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-primary">{{ $receipt->tenant->user->name ?? '—' }}</p>
                            <p class="text-xs text-muted">{{ $receipt->invoice->unit->unit_number ?? '—' }}</p>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-secondary">{{ $receipt->invoice->invoice_number ?? '—' }}</td>
                        <td class="px-6 py-4 font-bold text-success">KES {{ number_format($receipt->amount, 2) }}</td>
                        <td class="px-6 py-4 text-xs text-secondary">{{ $receipt->issued_at?->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('manager.receipts.download', $receipt) }}"
                                   class="text-xs bg-brand-600 hover:bg-brand-500 text-white px-3 py-1.5 rounded-lg font-medium transition-colors">
                                    Download
                                </a>
                                <form action="{{ route('manager.receipts.send', $receipt) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs bg-navy-500 hover:bg-navy-400 text-white px-3 py-1.5 rounded-lg font-medium transition-colors">
                                        Send Email
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($receipts->hasPages())
        <div class="px-6 py-4 border-t border-border">{{ $receipts->links() }}</div>
        @endif
        @endif
    </div>

</div>
@endsection
