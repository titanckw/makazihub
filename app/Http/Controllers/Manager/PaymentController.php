<?php
// app/Http/Controllers/Manager/PaymentController.php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Property;
use App\Services\InvoiceService;
use App\Services\MpesaService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private InvoiceService $invoiceService,
        private MpesaService   $mpesaService,
    ) {}

    /**
     * All payments list.
     */
    public function index(Request $request)
    {
        $manager = auth()->user();

        $payments = Payment::with(['invoice.unit.property', 'tenant.user', 'recorder'])
            ->whereHas('invoice.property', fn($q) => $q->where('manager_id', $manager->id))
            ->latest('paid_at')
            ->paginate(20);

        return view('manager.payments.index', compact('payments'));
    }

    /**
     * Show single payment.
     */
    public function show(Payment $payment)
    {
        $this->authorizePayment($payment);
        $payment->load(['invoice.unit.property', 'tenant.user', 'recorder', 'receipt']);
        return view('manager.payments.show', compact('payment'));
    }

    /**
     * Show record payment form for an invoice.
     */
    public function create(Request $request)
    {
        $request->validate(['invoice_id' => 'required|exists:invoices,id']);

        $invoice = Invoice::with(['tenant.user', 'unit.property', 'lease'])
            ->findOrFail($request->invoice_id);

        $this->authorizeInvoice($invoice);

        if ($invoice->status === 'paid') {
            return redirect()->route('manager.invoices.show', $invoice)
                ->with('error', 'This invoice is already fully paid.');
        }

        return view('manager.payments.create', compact('invoice'));
    }

    /**
     * Record a manual payment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id'     => 'required|exists:invoices,id',
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|in:mpesa,cash,bank_transfer,cheque',
            'reference'      => 'nullable|string|max:100',
            'mpesa_receipt'  => 'nullable|string|max:20',
            'phone_number'   => 'nullable|string|max:15',
            'paid_at'        => 'required|date',
            'notes'          => 'nullable|string|max:500',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);
        $this->authorizeInvoice($invoice);

        // Prevent overpayment
        if ($request->amount > $invoice->balance) {
            return back()->withInput()
                ->with('error', 'Amount exceeds outstanding balance of KES ' . number_format($invoice->balance, 2));
        }

        $payment = $this->invoiceService->recordPayment($invoice, $request->all());

        return redirect()->route('manager.payments.show', $payment)
            ->with('success', 'Payment of KES ' . number_format($payment->amount, 2) . ' recorded. Receipt generated.');
    }

    /**
     * Initiate M-Pesa STK Push to tenant's phone.
     */
    public function stkPush(Request $request)
    {
        $request->validate([
            'invoice_id'   => 'required|exists:invoices,id',
            'phone_number' => 'required|string',
            'amount'       => 'required|numeric|min:1',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);
        $this->authorizeInvoice($invoice);

        $result = $this->mpesaService->stkPush(
            phone:      $request->phone_number,
            amount:     $request->amount,
            accountRef: $invoice->invoice_number,
            description: 'Rent - ' . ($invoice->unit->unit_number ?? ''),
        );

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * M-Pesa Daraja callback endpoint (public, no auth).
     */
    public function mpesaCallback(Request $request)
    {
        $data   = $request->all();
        $result = $this->mpesaService->handleCallback($data);

        if (!$result['success']) {
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        // Try to match the payment to an invoice by account reference
        // The account reference is the invoice number set during STK push
        $accountRef = data_get($data, 'Body.stkCallback.CallbackMetadata.Item');
        $ref        = collect($accountRef)->firstWhere('Name', 'AccountReference')['Value'] ?? null;

        if ($ref) {
            $invoice = Invoice::where('invoice_number', $ref)
                ->whereIn('status', ['unpaid', 'partial', 'overdue'])
                ->first();

            if ($invoice) {
                $this->invoiceService->recordPayment($invoice, [
                    'amount'         => $result['amount'],
                    'payment_method' => 'mpesa',
                    'mpesa_receipt'  => $result['mpesa_receipt'],
                    'phone_number'   => $result['phone_number'],
                    'paid_at'        => now(),
                    'notes'          => 'Auto-recorded via M-Pesa callback',
                    'recorded_by'    => null,
                ]);
            }
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    /**
     * Reverse a payment (mark as reversed, restore invoice balance).
     */
    public function reverse(Request $request, Payment $payment)
    {
        $this->authorizePayment($payment);

        if ($payment->status === 'reversed') {
            return back()->with('error', 'Payment is already reversed.');
        }

        $invoice = $payment->invoice;
        $newAmountPaid = max(0, $invoice->amount_paid - $payment->amount);

        $payment->update(['status' => 'reversed']);
        $invoice->update([
            'amount_paid' => $newAmountPaid,
            'status'      => $newAmountPaid <= 0 ? 'unpaid' : 'partial',
        ]);

        return back()->with('success', 'Payment reversed. Invoice balance restored.');
    }

    /**
     * Confirm a pending payment.
     */
    public function confirm(Payment $payment)
    {
        $this->authorizePayment($payment);
        $payment->update(['status' => 'confirmed', 'confirmed_at' => now()]);
        return back()->with('success', 'Payment confirmed.');
    }

    private function authorizeInvoice(Invoice $invoice): void
    {
        $ok = Property::where('manager_id', auth()->id())
            ->where('id', $invoice->property_id)->exists();
        if (!$ok) abort(403);
    }

    private function authorizePayment(Payment $payment): void
    {
        $this->authorizeInvoice($payment->invoice);
    }
}
