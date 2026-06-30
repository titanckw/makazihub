<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #111827; }

        .page { max-width: 560px; margin: 0 auto; padding: 40px; }

        .header { text-align: center; margin-bottom: 32px; }
        .logo { font-size: 22px; font-weight: 700; color: #0F172A; }
        .logo-sub { font-size: 10px; color: #94A3B8; margin-top: 2px; }
        .receipt-title { font-size: 28px; font-weight: 700; color: #059669; margin-top: 16px; }
        .receipt-number { font-size: 13px; color: #475569; margin-top: 4px; font-family: monospace; }

        .stamp { background: #DCFCE7; border: 2px solid #16A34A; border-radius: 8px; padding: 12px 24px; display: inline-block; margin: 16px 0; }
        .stamp-text { font-size: 14px; font-weight: 700; color: #16A34A; text-transform: uppercase; letter-spacing: 2px; }

        .divider { border: none; border-top: 1px dashed #E2E8F0; margin: 20px 0; }
        .divider-solid { border: none; border-top: 2px solid #E2E8F0; margin: 20px 0; }

        .section-label { font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: #94A3B8; margin-bottom: 8px; }

        .detail-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 12px; }
        .detail-label { color: #475569; }
        .detail-value { font-weight: 600; color: #111827; text-align: right; }

        .amount-box { background: #F8FAFC; border-radius: 12px; padding: 20px; text-align: center; margin: 20px 0; }
        .amount-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #94A3B8; }
        .amount-value { font-size: 36px; font-weight: 700; color: #059669; margin-top: 4px; }
        .amount-words { font-size: 11px; color: #475569; margin-top: 4px; }

        .footer { text-align: center; margin-top: 32px; }
        .footer-note { font-size: 10px; color: #94A3B8; line-height: 1.6; }
        .footer-brand { font-size: 11px; font-weight: 600; color: #475569; margin-top: 8px; }
    </style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="logo">MakaziHub</div>
        <div class="logo-sub">Property Management Platform</div>
        <div class="receipt-title">Payment Receipt</div>
        <div class="receipt-number">{{ $receipt->receipt_number }}</div>
        <div style="text-align: center; margin-top: 12px;">
            <div class="stamp">
                <div class="stamp-text">✓ Payment Confirmed</div>
            </div>
        </div>
    </div>

    <hr class="divider-solid">

    {{-- Amount --}}
    <div class="amount-box">
        <div class="amount-label">Amount Received</div>
        <div class="amount-value">KES {{ number_format($receipt->amount, 2) }}</div>
        <div class="amount-words">Received on {{ $receipt->issued_at?->format('d F Y') }}</div>
    </div>

    <hr class="divider">

    {{-- Received From --}}
    <div class="section-label">Received From</div>
    <div class="detail-row">
        <span class="detail-label">Tenant Name</span>
        <span class="detail-value">{{ $receipt->tenant->user->name ?? '—' }}</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Email</span>
        <span class="detail-value">{{ $receipt->tenant->user->email ?? '—' }}</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Phone</span>
        <span class="detail-value">{{ $receipt->tenant->user->phone ?? '—' }}</span>
    </div>

    <hr class="divider">

    {{-- Payment Details --}}
    <div class="section-label">Payment Details</div>
    <div class="detail-row">
        <span class="detail-label">Invoice No.</span>
        <span class="detail-value" style="font-family: monospace">{{ $receipt->invoice->invoice_number ?? '—' }}</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Payment Method</span>
        <span class="detail-value">{{ $receipt->payment->method_label ?? '—' }}</span>
    </div>
    @if($receipt->payment?->mpesa_receipt)
    <div class="detail-row">
        <span class="detail-label">M-Pesa Code</span>
        <span class="detail-value" style="font-family: monospace">{{ $receipt->payment->mpesa_receipt }}</span>
    </div>
    @endif
    @if($receipt->payment?->reference && !$receipt->payment?->mpesa_receipt)
    <div class="detail-row">
        <span class="detail-label">Reference</span>
        <span class="detail-value" style="font-family: monospace">{{ $receipt->payment->reference }}</span>
    </div>
    @endif
    <div class="detail-row">
        <span class="detail-label">Payment Date</span>
        <span class="detail-value">{{ $receipt->payment?->paid_at ? \Carbon\Carbon::parse($receipt->payment->paid_at)->format('d M Y') : '—' }}</span>
    </div>

    <hr class="divider">

    {{-- Property Details --}}
    <div class="section-label">For</div>
    <div class="detail-row">
        <span class="detail-label">Property</span>
        <span class="detail-value">{{ $receipt->invoice->unit->property->name ?? '—' }}</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Unit</span>
        <span class="detail-value">{{ $receipt->invoice->unit->unit_number ?? '—' }}</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Rent Period</span>
        <span class="detail-value">
            {{ $receipt->invoice->period_start?->format('d M') }} – {{ $receipt->invoice->period_end?->format('d M Y') }}
        </span>
    </div>

    @if($receipt->invoice?->tax_note)
    <hr class="divider">
    <div class="section-label">Tax Compliance — {{ $receipt->invoice->tax_type_label }}</div>
    <div style="font-size:10px; color:#475569; line-height:1.5;">{{ $receipt->invoice->tax_note }}</div>
    @endif

    <hr class="divider-solid">

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-brand">MakaziHub — Property Management Platform</div>
        <div class="footer-note" style="margin-top: 6px">
            This is an official payment receipt. Please retain it for your records.<br>
            Receipt issued: {{ $receipt->issued_at?->format('d M Y, h:i A') }}
        </div>
    </div>

</div>
</body>
</html>
