{{-- resources/views/shared/notifications.blade.php --}}
@extends($layout)

@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('page-subtitle', 'Your SMS and email notification history')

@section('sidebar-nav')
    @if(auth()->user()->hasRole('manager'))
        @include('manager.partials.sidebar')
    @elseif(auth()->user()->hasRole('superadmin'))
        @include('superadmin.partials.sidebar')
    @else
        @include('tenant.partials.sidebar')
    @endif
@endsection

@section('content')
<div class="space-y-6">

    {{-- Page header --}}
    <div>
        <h1 class="text-2xl font-bold text-primary">Notifications</h1>
        <p class="text-sm text-muted mt-0.5">Your full SMS and email history</p>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-card rounded-2xl p-5 border-t-4 border-brand-600 shadow-sm">
            <p class="text-sm text-muted">Total Sent</p>
            <p class="text-3xl font-bold text-primary mt-1">{{ number_format($totalSent) }}</p>
            <p class="text-xs text-muted mt-1">All channels</p>
        </div>
        <div class="bg-card rounded-2xl p-5 border-t-4 border-danger shadow-sm">
            <p class="text-sm text-muted">Failed</p>
            <p class="text-3xl font-bold text-danger mt-1">{{ number_format($totalFailed) }}</p>
            <p class="text-xs text-muted mt-1">Delivery failures</p>
        </div>
        <div class="bg-card rounded-2xl p-5 border-t-4 border-info shadow-sm">
            <p class="text-sm text-muted">SMS</p>
            <p class="text-3xl font-bold text-primary mt-1">{{ number_format($smsSent) }}</p>
            <p class="text-xs text-muted mt-1">Messages sent</p>
        </div>
        <div class="bg-card rounded-2xl p-5 border-t-4 border-navy-500 shadow-sm">
            <p class="text-sm text-muted">Emails</p>
            <p class="text-3xl font-bold text-primary mt-1">{{ number_format($emailSent) }}</p>
            <p class="text-xs text-muted mt-1">Emails sent</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-card rounded-2xl shadow-sm border border-border p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="channel" class="px-3 py-2 rounded-xl border border-border text-sm bg-surface text-secondary focus:outline-none focus:border-brand-500">
                <option value="">All Channels</option>
                <option value="sms"   {{ request('channel') === 'sms'   ? 'selected' : '' }}>SMS</option>
                <option value="email" {{ request('channel') === 'email' ? 'selected' : '' }}>Email</option>
            </select>
            <select name="status" class="px-3 py-2 rounded-xl border border-border text-sm bg-surface text-secondary focus:outline-none focus:border-brand-500">
                <option value="">All Statuses</option>
                <option value="sent"   {{ request('status') === 'sent'   ? 'selected' : '' }}>Sent</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
            <select name="type" class="px-3 py-2 rounded-xl border border-border text-sm bg-surface text-secondary focus:outline-none focus:border-brand-500">
                <option value="">All Types</option>
                <option value="invoice_generated" {{ request('type') === 'invoice_generated' ? 'selected' : '' }}>Invoice Generated</option>
                <option value="payment_received"  {{ request('type') === 'payment_received'  ? 'selected' : '' }}>Payment Received</option>
                <option value="overdue_reminder"  {{ request('type') === 'overdue_reminder'  ? 'selected' : '' }}>Overdue Reminder</option>
                <option value="lease_expiry"      {{ request('type') === 'lease_expiry'      ? 'selected' : '' }}>Lease Expiry</option>
                <option value="welcome"           {{ request('type') === 'welcome'           ? 'selected' : '' }}>Welcome</option>
                <option value="custom"            {{ request('type') === 'custom'            ? 'selected' : '' }}>Custom</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-navy-500 hover:bg-navy-400 text-white text-sm font-semibold rounded-xl transition-colors">
                Filter
            </button>
            <a href="{{ url()->current() }}" class="px-4 py-2 text-sm font-medium text-secondary hover:text-primary rounded-xl transition-colors">
                Clear
            </a>
        </form>
    </div>

    {{-- Notifications list --}}
    <div class="bg-card rounded-2xl shadow-sm border border-border overflow-hidden">
        <div class="p-5 border-b border-border">
            <h2 class="font-semibold text-primary">History</h2>
            <p class="text-xs text-muted mt-0.5">{{ $notifications->total() }} notifications total</p>
        </div>

        @if($notifications->isEmpty())
        <div class="p-12 text-center text-muted">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5"/>
            </svg>
            <p class="font-medium">No notifications yet</p>
            <p class="text-sm mt-1">Notifications will appear here once invoices and payments are processed.</p>
        </div>
        @else
        <div class="divide-y divide-border">
            @foreach($notifications as $notif)
            <div class="flex items-start gap-4 px-5 py-4 hover:bg-surface/50 transition-colors">

                {{-- Icon --}}
                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 mt-0.5
                    {{ $notif->status === 'failed'
                        ? 'bg-danger-bg'
                        : ($notif->channel === 'sms' ? 'bg-info-bg' : 'bg-navy-100') }}">
                    @if($notif->status === 'failed')
                        <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    @elseif($notif->channel === 'sms')
                        <svg class="w-4 h-4 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    @else
                        <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="text-sm font-semibold text-primary">
                            {{ ucfirst(str_replace('_', ' ', $notif->type)) }}
                        </p>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                            {{ $notif->status === 'failed' ? 'bg-danger-bg text-danger' : 'bg-success-bg text-success' }}">
                            {{ ucfirst($notif->status) }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                            {{ $notif->channel === 'sms' ? 'bg-info-bg text-info' : 'bg-navy-100 text-secondary' }}">
                            {{ strtoupper($notif->channel) }}
                        </span>
                    </div>
                    <p class="text-xs text-muted mt-0.5">
                        Sent to <span class="font-mono">{{ $notif->recipient }}</span>
                    </p>
                    @if($notif->message)
                    <p class="text-xs text-secondary mt-1 line-clamp-2">{{ $notif->message }}</p>
                    @endif
                </div>

                {{-- Time --}}
                <p class="text-xs text-muted shrink-0 whitespace-nowrap">
                    {{ $notif->created_at->format('d M Y') }}<br>
                    <span class="text-[10px]">{{ $notif->created_at->format('H:i') }}</span>
                </p>

            </div>
            @endforeach
        </div>
        <div class="p-4 border-t border-border">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
