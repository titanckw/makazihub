{{-- resources/views/manager/notifications/index.blade.php --}}
@extends('layouts.manager')

@section('title', 'Notifications')

@section('sidebar-nav')
    @include('manager.partials.sidebar')
@endsection

@section('content')
<div class="space-y-6">

  {{-- Page Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold text-primary">Notifications</h1>
      <p class="text-sm text-muted mt-0.5">Send SMS & email alerts, and review delivery history</p>
    </div>
    <button onclick="document.getElementById('customSmsModal').classList.remove('hidden')"
            class="flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white text-sm font-semibold rounded-xl transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
      </svg>
      Send Custom SMS
    </button>
  </div>

  @if(session('success'))
  <div class="flex items-center gap-3 p-4 bg-success-bg border border-success/20 rounded-xl text-success text-sm font-medium">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ session('success') }}
  </div>
  @endif

  {{-- KPI Stats --}}
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
      <p class="text-sm text-muted">SMS Sent</p>
      <p class="text-3xl font-bold text-primary mt-1">{{ number_format($smsSent) }}</p>
      <p class="text-xs text-muted mt-1">Via Africa's Talking</p>
    </div>
    <div class="bg-card rounded-2xl p-5 border-t-4 border-navy-500 shadow-sm">
      <p class="text-sm text-muted">Emails Sent</p>
      <p class="text-3xl font-bold text-primary mt-1">{{ number_format($emailSent) }}</p>
      <p class="text-xs text-muted mt-1">Via Laravel Mail</p>
    </div>
  </div>

  {{-- Quick Actions --}}
  <div class="bg-card rounded-2xl shadow-sm border border-border p-5">
    <h2 class="font-semibold text-primary mb-4">Quick Actions</h2>
    <div class="flex flex-wrap gap-3">
      <form method="POST" action="{{ route('manager.notifications.send-all-overdue') }}">
        @csrf
        <button type="submit"
                onclick="return confirm('Send overdue reminders to ALL tenants with overdue invoices?')"
                class="flex items-center gap-2 px-4 py-2 bg-danger text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
          </svg>
          Send All Overdue Reminders
        </button>
      </form>
    </div>
  </div>

  {{-- Filters --}}
  <div class="bg-card rounded-2xl shadow-sm border border-border p-5">
    <form method="GET" class="flex flex-wrap gap-3">
      <select name="channel" class="px-3 py-2 rounded-xl border border-border text-sm bg-surface text-secondary focus:outline-none focus:border-brand-500">
        <option value="">All Channels</option>
        <option value="sms"   {{ request('channel') === 'sms'   ? 'selected' : '' }}>SMS</option>
        <option value="email" {{ request('channel') === 'email' ? 'selected' : '' }}>Email</option>
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
      <select name="status" class="px-3 py-2 rounded-xl border border-border text-sm bg-surface text-secondary focus:outline-none focus:border-brand-500">
        <option value="">All Statuses</option>
        <option value="sent"   {{ request('status') === 'sent'   ? 'selected' : '' }}>Sent</option>
        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
      </select>
      <button type="submit" class="px-4 py-2 bg-navy-500 hover:bg-navy-400 text-white text-sm font-semibold rounded-xl transition-colors">Filter</button>
      <a href="{{ route('manager.notifications.index') }}" class="px-4 py-2 text-sm font-medium text-secondary hover:text-primary rounded-xl transition-colors">Clear</a>
    </form>
  </div>

  {{-- Notifications Log Table --}}
  <div class="bg-card rounded-2xl shadow-sm border border-border overflow-hidden">
    <div class="p-5 border-b border-border">
      <h2 class="font-semibold text-primary">Notification History</h2>
      <p class="text-xs text-muted mt-0.5">{{ $logs->total() }} notifications found</p>
    </div>

    @if($logs->isEmpty())
    <div class="p-12 text-center text-muted">
      <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5"/>
      </svg>
      <p class="font-medium">No notifications yet</p>
      <p class="text-sm mt-1">Notifications will appear here once invoices and payments are processed.</p>
    </div>
    @else
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-border bg-surface">
            <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase tracking-wide">Date</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase tracking-wide">Recipient</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase tracking-wide">Channel</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase tracking-wide">Type</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase tracking-wide">Sent To</th>
            <th class="text-left px-5 py-3 text-xs font-semibold text-muted uppercase tracking-wide">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border">
          @foreach($logs as $log)
          <tr class="hover:bg-surface/60 transition-colors">
            <td class="px-5 py-3.5 text-muted text-xs whitespace-nowrap">{{ $log->created_at->format('d M Y, H:i') }}</td>
            <td class="px-5 py-3.5 font-medium text-primary">{{ $log->recipient?->name ?? '—' }}</td>
            <td class="px-5 py-3.5">
              <span class="flex items-center gap-1.5 text-xs font-medium
                @if($log->channel === 'sms') text-info @else text-secondary @endif">
                @if($log->channel === 'sms')
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                SMS
                @else
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Email
                @endif
              </span>
            </td>
            <td class="px-5 py-3.5 text-secondary">{{ $log->type_label }}</td>
            <td class="px-5 py-3.5 text-muted font-mono text-xs">{{ $log->recipient }}</td>
            <td class="px-5 py-3.5">
              <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $log->status_badge }}">
                {{ ucfirst($log->status) }}
              </span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="p-4 border-t border-border">
      {{ $logs->links() }}
    </div>
    @endif
  </div>

</div>

{{-- Custom SMS Modal --}}
<div id="customSmsModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
  <div class="bg-card rounded-2xl shadow-xl w-full max-w-lg">
    <div class="p-6 border-b border-border flex items-center justify-between">
      <h2 class="font-semibold text-primary">Send Custom SMS</h2>
      <button onclick="document.getElementById('customSmsModal').classList.add('hidden')"
              class="text-muted hover:text-primary transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    <form method="POST" action="{{ route('manager.notifications.send-custom') }}" class="p-6 space-y-4">
      @csrf

      <div>
        <label class="block text-sm font-medium text-secondary mb-1.5">Select Tenants</label>
        <div class="space-y-2 max-h-48 overflow-y-auto border border-border rounded-xl p-3 bg-surface">
          @foreach($tenants as $tenant)
          <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" name="tenant_ids[]" value="{{ $tenant->id }}"
                   class="w-4 h-4 rounded border-border text-brand-600 focus:ring-brand-500">
            <div>
              <span class="text-sm font-medium text-primary">{{ $tenant->user->name }}</span>
              <span class="text-xs text-muted ml-2">{{ $tenant->user->phone ?? 'No phone' }}</span>
            </div>
          </label>
          @endforeach
        </div>
        <button type="button" onclick="toggleAll()"
                class="mt-2 text-xs text-brand-600 hover:text-brand-500 font-medium">Toggle All</button>
      </div>

      <div>
        <label class="block text-sm font-medium text-secondary mb-1.5">
          Message <span class="text-muted" id="charCount">(0 / 160)</span>
        </label>
        <textarea name="message" rows="4" maxlength="160"
                  oninput="document.getElementById('charCount').textContent='(' + this.value.length + ' / 160)'"
                  class="w-full px-4 py-3 rounded-xl border border-border bg-white text-sm text-primary focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 resize-none"
                  placeholder="Type your SMS message here..."></textarea>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit" class="flex-1 py-2.5 bg-brand-600 hover:bg-brand-500 text-white text-sm font-semibold rounded-xl transition-colors">
          Send SMS
        </button>
        <button type="button" onclick="document.getElementById('customSmsModal').classList.add('hidden')"
                class="px-6 py-2.5 text-sm font-semibold text-secondary hover:text-primary border border-border rounded-xl transition-colors">
          Cancel
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleAll() {
  const boxes = document.querySelectorAll('input[name="tenant_ids[]"]');
  const allChecked = Array.from(boxes).every(b => b.checked);
  boxes.forEach(b => b.checked = !allChecked);
}
</script>
@endsection
