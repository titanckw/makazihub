{{-- resources/views/emails/invoice-generated.blade.php --}}
<x-emails.partials.layout>

  <p class="greeting">Dear {{ $invoice->tenant->user->name }},</p>
  <h2>Your Rent Invoice is Ready</h2>
  <p class="subtitle">A new invoice has been generated for your tenancy at {{ $invoice->property->name }}.</p>

  <div class="card">
    <div class="card-row">
      <span class="card-label">Invoice Number</span>
      <span class="card-value">{{ $invoice->invoice_number }}</span>
    </div>
    <div class="card-row">
      <span class="card-label">Property</span>
      <span class="card-value">{{ $invoice->property->name }}</span>
    </div>
    <div class="card-row">
      <span class="card-label">Unit</span>
      <span class="card-value">{{ $invoice->unit->unit_number }}</span>
    </div>
    <div class="card-row">
      <span class="card-label">Period</span>
      <span class="card-value">{{ $invoice->period_start->format('M d') }} – {{ $invoice->period_end->format('M d, Y') }}</span>
    </div>
    <div class="card-row">
      <span class="card-label">Amount Due</span>
      <span class="card-value">
        <div class="amount-highlight">KES {{ number_format($invoice->amount, 2) }}</div>
      </span>
    </div>
    <div class="card-row">
      <span class="card-label">Due Date</span>
      <span class="card-value" style="color: #D97706;">{{ $invoice->due_date->format('M d, Y') }}</span>
    </div>
    <div class="card-row">
      <span class="card-label">Status</span>
      <span class="card-value"><span class="badge badge-unpaid">Unpaid</span></span>
    </div>
  </div>

  <div class="warning-box">
    ⚠️ Please ensure payment is made before <strong>{{ $invoice->due_date->format('F d, Y') }}</strong> to avoid late fees.
  </div>

  <p style="font-size:14px; color:#475569; margin-bottom:20px;">
    You can pay via M-Pesa Paybill or ask your property manager to initiate an STK Push to your phone. Log in to the tenant portal to view your invoice and payment history.
  </p>

  <a href="#" class="btn">View Invoice in Portal</a>

  <hr class="divider">

  <p style="font-size:13px; color:#94A3B8;">
    Invoice generated on {{ $invoice->created_at->format('M d, Y \a\t H:i') }}. 
    If you believe this invoice was sent in error, please contact your property manager.
  </p>

</x-emails.partials.layout>
