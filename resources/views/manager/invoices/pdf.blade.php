<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #111827; background: #fff; }

        .header { background: #0F172A; color: white; padding: 32px 40px; }
        .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
        .logo-text { font-size: 24px; font-weight: 700; letter-spacing: -0.5px; }
        .logo-sub { font-size: 10px; color: rgba(255,255,255,0.5); margin-top: 2px; }
        .invoice-badge { background: #059669; color: white; padding: 6px 16px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
        .invoice-number { font-size: 28px; font-weight: 700; margin-top: 20px; }
        .invoice-meta { font-size: 10px; color: rgba(255,255,255,0.6); margin-top: 4px; }

        .content { padding: 32px 40px; }

        .two-col { display: flex; gap: 24px; margin-bottom: 28px; }
        .col { flex: 1; }
        .col-label { font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: #94A3B8; margin-bottom: 8px; }
        .col-value { font-size: 13px; font-weight: 600; color: #111827; line-height: 1.5; }
        .col-sub { font-size: 11px; color: #475569; margin-top: 2px; }

        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 10px; font-weight: 600; text-transform: uppercase; }
        .status-paid    { background: #DCFCE7; color: #16A34A; }
        .status-overdue { background: #FEE2E2; color: #DC2626; }
        .status-partial { background: #DBEAFE; color: #2563EB; }
        .status-unpaid  { background: #FEF3C7; color: #D97706; }

        .divider { border: none; border-top: 1px solid #E2E8F0; margin: 20px 0; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        thead tr { background: #F8FAFC; }
        th { text-align: left; padding: 10px 14px; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #475569; border-bottom: 2px solid #E2E8F0; }
        td { padding: 12px 14px; font-size: 12px; color: #111827; border-bottom: 1px solid #F1F5F9; }

        .totals { margin-left: auto; width: 260px; }
        .total-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 12px; }
        .total-row.final { border-top: 2px solid #0F172A; margin-top: 6px; padding-top: 10px; }
        .total-row.final .label { font-weight: 700; font-size: 14px; }
        .total-row.final .value { font-weight: 700; font-size: 16px; color: {{ $invoice->balance > 0 ? '#DC2626' : '#16A34A' }}; }

        .payment-history { margin-top: 24px; }
        .ph-title { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #475569; margin-bottom: 10px; }

        .footer { background: #F8FAFC; border-top: 1px solid #E2E8F0; padding: 20px 40px; margin-top: 32px; }
        .footer-note { font-size: 10px; color: #94A3B8; line-height: 1.6; }
        .footer-brand { font-size: 11px; font-weight: 600; color: #475569; }
    </style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <div class="header-top">
        <div>
            <div class="logo-text">MakaziHub</div>
            <div class="logo-sub">Property Management Platform</div>
        </div>
        <div class="invoice-badge">Invoice</div>
    </div>
    <div class="invoice-number">{{ $invoice->invoice_number }}</div>
    <div class="invoice-meta">
        Generated: {{ now()->format('d M Y') }} &nbsp;·&nbsp;
        Period: {{ $invoice->period_start->format('d M Y') }} – {{ $invoice->period_end->format('d M Y') }}
    </div>
</div>

{{-- Main Content --}}
<div class="content">

    {{-- Billed To / Property --}}
    <div class="two-col">
        <div class="col">
            <div class="col-label">Billed To</div>
            <div class="col-value">{{ $invoice->tenant->user->name ?? '—' }}</div>
            <div class="col-sub">{{ $invoice->tenant->user->email ?? '' }}</div>
            <div class="col-sub">{{ $invoice->tenant->user->phone ?? '' }}</div>
            <div class="col-sub">ID: {{ $invoice->tenant->id_number ?? '' }}</div>
        </div>
        <div class="col">
            <div class="col-label">Property</div>
            <div class="col-value">{{ $invoice->unit->property->name ?? '—' }}</div>
            <div class="col-sub">Unit {{ $invoice->unit->unit_number ?? '—' }}</div>
        </div>
        <div class="col">
            <div class="col-label">Invoice Status</div>
            <div style="margin-top: 4px">
                <span class="status-badge status-{{ $invoice->status }}">{{ $invoice->status_label }}</span>
            </div>
            <div class="col-sub" style="margin-top: 8px">Due: {{ $invoice->due_date->format('d M Y') }}</div>
        </div>
    </div>

    <hr class="divider">

    {{-- Line Items --}}
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Period</th>
                <th style="text-align: right">Amount (KES)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Monthly Rent — Unit {{ $invoice->unit->unit_number ?? '—' }}</td>
                <td>{{ $invoice->period_start->format('d M Y') }} – {{ $invoice->period_end->format('d M Y') }}</td>
                <td style="text-align: right; font-weight: 600">{{ number_format($invoice->amount_due, 2) }}</td>
            </tr>
            @if($invoice->vat_amount > 0)
            <tr>
                <td>VAT (16%) on Commercial Rent</td>
                <td>—</td>
                <td style="text-align: right; font-weight: 600">{{ number_format($invoice->vat_amount, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    @if($invoice->tax_note)
    <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:6px; padding:12px 14px; margin-bottom:20px;">
        <div style="font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:#94A3B8; margin-bottom:4px;">
            Tax Compliance — {{ $invoice->tax_type_label }}
        </div>
        <div style="font-size:10px; color:#475569; line-height:1.5;">{{ $invoice->tax_note }}</div>
    </div>
    @endif

    {{-- Totals --}}
    <div class="totals">
        <div class="total-row">
            <span class="label" style="color:#475569">Invoice Amount</span>
            <span class="value">{{ number_format($invoice->amount, 2) }}</span>
        </div>
        <div class="total-row">
            <span class="label" style="color:#16A34A">Amount Paid</span>
            <span class="value" style="color:#16A34A">{{ number_format($invoice->amount_paid, 2) }}</span>
        </div>
        <div class="total-row final">
            <span class="label">Balance Due</span>
            <span class="value">{{ number_format($invoice->balance, 2) }}</span>
        </div>
    </div>

    {{-- Payment History --}}
    @if($invoice->payments->count() > 0)
    <div class="payment-history">
        <hr class="divider">
        <div class="ph-title">Payment History</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Reference</th>
                    <th style="text-align: right">Amount (KES)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments->where('status','confirmed') as $payment)
                <tr>
                    <td>{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y') : '—' }}</td>
                    <td>{{ $payment->method_label }}</td>
                    <td>{{ $payment->mpesa_receipt ?? $payment->reference ?? '—' }}</td>
                    <td style="text-align: right; font-weight: 600; color:#16A34A">{{ number_format($payment->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>

{{-- Footer --}}
<div class="footer">
    <div class="footer-brand">MakaziHub — Property Management Platform</div>
    <div class="footer-note" style="margin-top: 4px">
        This is a computer-generated invoice. Please make payments via M-Pesa Paybill or contact your property manager for assistance.
        For queries, contact your property manager directly.
    </div>
</div>

</body>
</html>
