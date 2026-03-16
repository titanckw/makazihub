<?php
// app/Services/NotificationService.php

namespace App\Services;

use App\Mail\InvoiceGeneratedMail;
use App\Mail\OverdueReminderMail;
use App\Mail\PaymentReceivedMail;
use App\Mail\WelcomeTenantMail;
use App\Models\Invoice;
use App\Models\NotificationLog;
use App\Models\Payment;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function __construct(protected SmsService $sms)
    {
    }

    // ── Invoice Generated ──────────────────────────────────────────

    public function invoiceGenerated(Invoice $invoice): void
    {
        $tenant = $invoice->tenant->load('user');
        $user = $tenant->user;

        // SMS
        $this->sms->invoiceGenerated(
            phone: $user->phone ?? '',
            tenantName: $user->name,
            invoiceNumber: $invoice->invoice_number,
            amount: (float) $invoice->total_amount,
            dueDate: $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') : '',
            userId: $user->id,
        );

        // Email
        $this->sendEmail($user->email, new InvoiceGeneratedMail($invoice), $user->id, 'invoice_generated');
    }

    // ── Payment Received ───────────────────────────────────────────

    public function paymentReceived(Payment $payment): void
    {
        $invoice = $payment->invoice->load('tenant.user', 'property', 'unit', 'receipt');
        $user = $invoice->tenant->user;

        // SMS
        $this->sms->paymentReceived(
            phone: $user->phone ?? '',
            tenantName: $user->name,
            amount: (float) $payment->amount,
            receiptNumber: $invoice->receipt?->receipt_number ?? 'N/A',
            period: $invoice->period_start->format('M Y'),
            userId: $user->id,
        );

        // Email
        $this->sendEmail($user->email, new PaymentReceivedMail($payment), $user->id, 'payment_received');
    }

    // ── Overdue Reminder ───────────────────────────────────────────

    public function overdueReminder(Invoice $invoice): void
    {
        $invoice->load('tenant.user', 'property', 'unit');
        $user = $invoice->tenant->user;
        $daysOverdue = (int) \Carbon\Carbon::parse($invoice->due_date)->diffInDays(now());

        // SMS
        $this->sms->overdueReminder(
            phone: $user->phone ?? '',
            tenantName: $user->name,
            invoiceNumber: $invoice->invoice_number,
            balance: $invoice->balance,
            daysOverdue: $daysOverdue,
            userId: $user->id,
        );

        // Email
        $this->sendEmail($user->email, new OverdueReminderMail($invoice, $daysOverdue), $user->id, 'overdue_reminder');
    }

    // ── Welcome Tenant ─────────────────────────────────────────────

    public function welcomeTenant(Tenant $tenant): void
    {
        $tenant->load('user', 'activeLease.unit.property');
        $user = $tenant->user;
        $lease = $tenant->activeLease;

        // SMS
        $this->sms->welcomeTenant(
            phone: $user->phone ?? '',
            tenantName: $user->name,
            unitName: $lease?->unit?->unit_number ?? 'your unit',
            propertyName: $lease?->unit?->property?->name ?? 'your property',
            userId: $user->id,
        );

        // Email
        $this->sendEmail($user->email, new WelcomeTenantMail($tenant), $user->id, 'welcome');
    }

    // ── Custom / Broadcast ─────────────────────────────────────────

    /**
     * Send a custom SMS to one or many tenants.
     *
     * @param  array  $tenants   Collection/array of Tenant models
     * @param  string $message
     */
    public function customSms(array|\Illuminate\Support\Collection $tenants, string $message): int
    {
        $sent = 0;
        foreach ($tenants as $tenant) {
            $user = $tenant->user;
            if ($this->sms->send($user->phone ?? '', $message, $user->id, 'custom')) {
                $sent++;
            }
        }
        return $sent;
    }

    // ── Private Helpers ────────────────────────────────────────────

    protected function sendEmail(string $email, \Illuminate\Mail\Mailable $mailable, int $userId, string $type): void
    {
        try {
            Mail::to($email)->send($mailable);
            NotificationLog::create([
                'user_id' => $userId,
                'type' => $type,
                'channel' => 'email',
                'recipient' => $email,
                'subject' => $type,
                'message' => class_basename($mailable),
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Email send failed: ' . $e->getMessage());
            NotificationLog::create([
                'user_id' => $userId,
                'type' => $type,
                'channel' => 'email',
                'recipient' => $email,
                'subject' => $type,
                'message' => class_basename($mailable),
                'status' => 'failed',
                'raw_response' => $e->getMessage(),
            ]);
        }
    }
}
