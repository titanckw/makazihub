<?php
// app/Services/InvoiceService.php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Receipt;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Generate a single invoice for a lease for the current (or specified) month.
     */
    public function generateForLease(Lease $lease, ?Carbon $forDate = null): Invoice
    {
        $forDate = $forDate ?? now();

        $periodStart = $forDate->copy()->startOfMonth();
        $periodEnd = $forDate->copy()->endOfMonth();
        $dueDate = $forDate->copy()->setDay(min($lease->payment_day, $forDate->daysInMonth));

        // Prevent duplicate invoices for the same period
        $exists = Invoice::where('lease_id', $lease->id)
            ->whereYear('period_start', $periodStart->year)
            ->whereMonth('period_start', $periodStart->month)
            ->first();

        if ($exists) {
            return $exists;
        }

        return DB::transaction(function () use ($lease, $periodStart, $periodEnd, $dueDate, $forDate) {
            $rent = $lease->rent_amount;

            return Invoice::create([
                'lease_id' => $lease->id,
                'tenant_id' => $lease->tenant_id,
                'unit_id' => $lease->unit_id,
                'property_id' => $lease->property_id,
                'invoice_number' => Invoice::generateNumber(),
                'amount_due' => $rent,
                'late_fee' => 0,
                'total_amount' => $rent, // currently same as amount_due
                'amount_paid' => 0,
                'due_date' => $dueDate,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'invoice_date' => $forDate->toDateString(),
                'billing_period' => $periodStart->format('Y-m'),
                'status' => 'unpaid',
                'generated_by' => 'manual',
            ]);
        });
    }

    /**
     * Auto-generate invoices for ALL active leases (called by cron on 1st of month).
     * Returns count of invoices generated.
     */
    public function generateMonthly(): int
    {
        $leases = Lease::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        $count = 0;
        foreach ($leases as $lease) {
            $existing = Invoice::where('lease_id', $lease->id)
                ->whereYear('period_start', now()->year)
                ->whereMonth('period_start', now()->month)
                ->exists();

            if (!$existing) {
                $this->generateForLease($lease instanceof Lease ? $lease : Lease::find($lease->id));
                $count++;
            }
        }

        return $count;
    }

    /**
     * Record a payment against an invoice and update invoice status.
     * Also creates a receipt automatically.
     */
    public function recordPayment(Invoice $invoice, array $data): Payment
    {
        return DB::transaction(function () use ($invoice, $data) {
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'tenant_id' => $invoice->tenant_id,
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
                'reference' => $data['reference'] ?? null,
                'mpesa_receipt' => $data['mpesa_receipt'] ?? null,
                'phone_number' => $data['phone_number'] ?? null,
                'status' => 'confirmed',
                // record both date and datetime for compatibility with legacy views
                'paid_at' => $data['paid_at'] ?? now(),
                'payment_date' => isset($data['paid_at'])
                    ? \Carbon\Carbon::parse($data['paid_at'])->toDateString()
                    : now()->toDateString(),
                'confirmed_at' => now(),
                'notes' => $data['notes'] ?? null,
                'recorded_by' => auth()->id(),
            ]);

            // Update invoice paid amount
            $newAmountPaid = $invoice->amount_paid + $payment->amount;
            $newStatus = $this->resolveInvoiceStatus((float) $invoice->total_amount, $newAmountPaid, Carbon::parse($invoice->due_date));

            $invoice->update([
                'amount_paid' => $newAmountPaid,
                'status' => $newStatus,
            ]);

            // Auto-generate receipt
            Receipt::create([
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'tenant_id' => $invoice->tenant_id,
                'receipt_number' => Receipt::generateNumber(),
                'amount' => $payment->amount,
                'issued_at' => now(),
            ]);

            return $payment;
        });
    }

    /**
     * Mark overdue invoices — run daily via cron.
     */
    public function markOverdueInvoices(): int
    {
        return Invoice::where('status', 'unpaid')
            ->where('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);
    }

    /**
     * Determine invoice status based on amounts and due date.
     */
    private function resolveInvoiceStatus(float $amount, float $amountPaid, Carbon $dueDate): string
    {
        if ($amountPaid >= $amount) {
            return 'paid';
        }

        if ($amountPaid > 0) {
            return 'partial';
        }

        return $dueDate->isPast() ? 'overdue' : 'unpaid';
    }
}
