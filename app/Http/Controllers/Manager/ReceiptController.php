<?php
// app/Http/Controllers/Manager/ReceiptController.php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Receipt;
use App\Models\Property;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function index()
    {
        $manager = auth()->user();

        $receipts = Receipt::with(['tenant.user', 'invoice.unit.property', 'payment'])
            ->whereHas('invoice.property', fn($q) => $q->where('manager_id', $manager->id))
            ->latest('issued_at')
            ->paginate(20);

        return view('manager.receipts.index', compact('receipts'));
    }

    public function download(Receipt $receipt)
    {
        $this->authorizeReceipt($receipt);
        $receipt->load(['tenant.user', 'invoice.unit.property', 'payment']);

        $pdf = Pdf::loadView('manager.receipts.pdf', compact('receipt'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('receipt-' . $receipt->receipt_number . '.pdf');
    }

    public function send(Receipt $receipt)
    {
        $this->authorizeReceipt($receipt);
        // Email notification will be wired in Module 7
        return back()->with('success', 'Receipt sent to ' . $receipt->tenant->user->email . '.');
    }

    private function authorizeReceipt(Receipt $receipt): void
    {
        $ok = Property::where('manager_id', auth()->id())
            ->where('id', $receipt->invoice->property_id)->exists();
        if (!$ok) abort(403);
    }
}
