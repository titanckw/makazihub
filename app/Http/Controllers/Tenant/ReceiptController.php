<?php
// app/Http/Controllers/Tenant/ReceiptController.php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function download(Receipt $receipt)
    {
        $tenant = Tenant::where('user_id', auth()->id())->firstOrFail();
        abort_unless($receipt->invoice->tenant_id === $tenant->id, 403);

        $receipt->load(['invoice.unit', 'invoice.property', 'invoice.tenant.user', 'payment']);

        $pdf = Pdf::loadView('manager.receipts.pdf', compact('receipt'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('receipt-' . $receipt->receipt_number . '.pdf');
    }
}
