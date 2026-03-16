{{-- resources/views/emails/payment-received.blade.php --}}
<x-emails.partials.layout>

  <p class="greeting">Dear {{ $payment->invoice->tenant->user->name }},</p>
  <h2>Payment Confirmed ✓</h2>
  <p class="subtitle">We've received your rent payment. Thank you!</p>

  <div class="success-box">
    ✅ Your payment of <strong>KES {{ number_format($payment->amount, 2) }}</strong> has been successfully recorded.
  </div>

  <div class="card">
    <div class="card-row">
      <span class="card-label">Receipt Number</span>
      <span class="card-value">{{ $payment->invoice->receipt?->receipt_number ?? 'Processing...' }}</span>
    </div>
    <div class="card-row">
      <span class="card-label">Invoice</span>
      <span class="card-value">{{ $payment->invoice->invoice_number }}</span>
    </div>
    <div class="card-row">
      <span class="card-label">Property</span>
      <span class="card-value">{{ $payment->invoice->property->name }}</span>
    </div>
    <div class="card-row">
      <span class="card-label">Unit</span>
      <span class="card-value">{{ $payment->invoice->unit->unit_number }}</span>
    </div>
    <div class="card-row">
      <span class="card-label">Amount Paid</span>
      <span class="card-value">
        <div class="amount-highlight">KES {{ number_format($payment->amount, 2) }}</div>
      </span>
    </div>
    <div class="card-row">
      <span class="card-label">Payment Method</span>
      <span class="card-value">{{ strtoupper($payment->payment_method) }}</span>
    </div>
    @if($payment->mpesa_transaction_id)
    <div class="card-row">
      <span class="card-label">M-Pesa Code</span>
      <span class="card-value" style="font-family: monospace; letter-spacing: 1px;">{{ $payment->mpesa_transaction_id }}</span>
    </div>
    @endif
    <div class="card-row">
      <span class="card-label">Payment Date</span>
      <span class="card-value">{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('M d, Y H:i') : $payment->created_at->format('M d, Y H:i') }}</span>
    </div>
    <div class="card-row">
      <span class="card-label">Invoice Status</span>
      <span class="card-value">
        <span class="badge badge-{{ $payment->invoice->status }}">{{ ucfirst($payment->invoice->status) }}</span>
      </span>
    </div>
    @if($payment->invoice->balance > 0)
    <div class="card-row">
      <span class="card-label">Remaining Balance</span>
      <span class="card-value" style="color: #D97706;">KES {{ number_format($payment->invoice->balance, 2) }}</span>
    </div>
    @endif
  </div>

  @if($payment->invoice->balance > 0)
  <div class="warning-box">
    ⚠️ There is still an outstanding balance of <strong>KES {{ number_format($payment->invoice->balance, 2) }}</strong> on this invoice. Please complete the payment by <strong>{{ $payment->invoice->due_date->format('M d, Y') }}</strong>.
  </div>
  @endif

  <a href="#" class="btn">Download Receipt</a>

  <hr class="divider">
  <p style="font-size:13px; color:#94A3B8;">Keep this email as proof of payment. Your official receipt is also available in the tenant portal.</p>

</x-emails.partials.layout>
