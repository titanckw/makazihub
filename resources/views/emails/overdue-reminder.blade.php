{{-- resources/views/emails/overdue-reminder.blade.php --}}
<x-emails.partials.layout>

  <p class="greeting">Dear {{ $invoice->tenant->user->name }},</p>
  <h2>Overdue Rent Notice</h2>
  <p class="subtitle">Your rent payment is overdue. Please take immediate action.</p>

  <div class="danger-box">
    🔴 Invoice <strong>{{ $invoice->invoice_number }}</strong> is <strong>{{ $daysOverdue }} day(s) overdue</strong> as of today.
  </div>

  <div class="card">
    <div class="card-row">
      <span class="card-label">Invoice Number</span>
      <span class="card-value">{{ $invoice->invoice_number }}</span>
    </div>
    <div class="card-row">
      <span class="card-label">Property / Unit</span>
      <span class="card-value">{{ $invoice->property->name }} — {{ $invoice->unit->unit_number }}</span>
    </div>
    <div class="card-row">
      <span class="card-label">Rental Period</span>
      <span class="card-value">{{ $invoice->period_start->format('M d') }} – {{ $invoice->period_end->format('M d, Y') }}</span>
    </div>
    <div class="card-row">
      <span class="card-label">Original Amount</span>
      <span class="card-value">KES {{ number_format($invoice->amount, 2) }}</span>
    </div>
    @if($invoice->amount_paid > 0)
    <div class="card-row">
      <span class="card-label">Amount Paid</span>
      <span class="card-value" style="color: #16A34A;">KES {{ number_format($invoice->amount_paid, 2) }}</span>
    </div>
    @endif
    <div class="card-row">
      <span class="card-label">Outstanding Balance</span>
      <span class="card-value">
        <div class="amount-highlight" style="color: #DC2626;">KES {{ number_format($invoice->balance, 2) }}</div>
      </span>
    </div>
    <div class="card-row">
      <span class="card-label">Due Date</span>
      <span class="card-value" style="color: #DC2626;">{{ $invoice->due_date->format('M d, Y') }} ({{ $daysOverdue }} days ago)</span>
    </div>
  </div>

  <p style="font-size:14px; color:#475569; margin-bottom:20px; line-height:1.6;">
    Continued non-payment may result in a <strong>late fee</strong> and could affect your tenancy. Please contact your property manager immediately if you are facing difficulties making this payment.
  </p>

  <a href="#" class="btn" style="background: #DC2626;">Pay Now</a>

  <hr class="divider">
  <p style="font-size:13px; color:#94A3B8;">
    If you have already made this payment, please ignore this notice and allow up to 24 hours for it to reflect. 
    For disputes, contact your property manager directly.
  </p>

</x-emails.partials.layout>
