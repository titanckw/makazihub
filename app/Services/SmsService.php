<?php
// app/Services/SmsService.php

namespace App\Services;

use App\Models\NotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected string $apiKey;
    protected string $username;
    protected string $senderId;
    protected bool $sandbox;

    public function __construct()
    {
        $this->apiKey = config('africastalking.api_key');
        $this->username = config('africastalking.username');
        $this->senderId = config('africastalking.sender_id', 'MAKAZIHUB');
        $this->sandbox = config('africastalking.sandbox', true);
    }

    /**
     * Send a single SMS.
     *
     * @param  string  $phone   E.164 or local format (e.g. 0712345678 or +254712345678)
     * @param  string  $message
     * @param  int|null $recipientId   User ID for logging
     * @param  string   $type          Notification type label
     * @return bool
     */
    public function send(string $phone, string $message, ?int $recipientId = null, string $type = 'general'): bool
    {
        $phone = $this->normalizePhone($phone);

        try {
            $baseUrl = $this->sandbox
                ? 'https://api.sandbox.africastalking.com/version1/messaging'
                : 'https://api.africastalking.com/version1/messaging';

            $response = Http::withHeaders([
                'apiKey' => $this->apiKey,
                'Accept' => 'application/json',
            ])->asForm()->post($baseUrl, [
                        'username' => $this->username,
                        'to' => $phone,
                        'message' => $message,
                    ]);

            $success = $response->successful() &&
                isset($response->json()['SMSMessageData']['Recipients'][0]['status']) &&
                str_contains($response->json()['SMSMessageData']['Recipients'][0]['status'], 'Success');

            $this->log($recipientId, $type, $phone, $message, 'sms', $success, $response->body());

            return $success;

        } catch (\Throwable $e) {
            Log::error('SmsService error: ' . $e->getMessage());
            $this->log($recipientId, $type, $phone, $message, 'sms', false, $e->getMessage());
            return false;
        }
    }

    /**
     * Send the same SMS to multiple recipients.
     */
    public function sendBulk(array $recipients, string $message, string $type = 'general'): int
    {
        $sent = 0;
        foreach ($recipients as $recipient) {
            $phone = is_array($recipient) ? ($recipient['phone'] ?? '') : $recipient;
            $id = is_array($recipient) ? ($recipient['id'] ?? null) : null;
            if ($this->send($phone, $message, $id, $type)) {
                $sent++;
            }
        }
        return $sent;
    }

    // ── Templated Messages ─────────────────────────────────────────

    public function invoiceGenerated(string $phone, string $tenantName, string $invoiceNumber, float $amount, string $dueDate, ?int $userId = null): bool
    {
        $message = "Dear {$tenantName}, your rent invoice {$invoiceNumber} of KES " . number_format($amount, 2) . " is due on {$dueDate}. Log in to MakaziHub to view details. -MAKAZIHUB";
        return $this->send($phone, $message, $userId, 'invoice_generated');
    }

    public function paymentReceived(string $phone, string $tenantName, float $amount, string $receiptNumber, string $period, ?int $userId = null): bool
    {
        $message = "Dear {$tenantName}, payment of KES " . number_format($amount, 2) . " received for {$period}. Receipt: {$receiptNumber}. Thank you! -MAKAZIHUB";
        return $this->send($phone, $message, $userId, 'payment_received');
    }

    public function overdueReminder(string $phone, string $tenantName, string $invoiceNumber, float $balance, int $daysOverdue, ?int $userId = null): bool
    {
        $message = "Dear {$tenantName}, invoice {$invoiceNumber} is {$daysOverdue} day(s) overdue. Outstanding balance: KES " . number_format($balance, 2) . ". Please settle immediately. -MAKAZIHUB";
        return $this->send($phone, $message, $userId, 'overdue_reminder');
    }

    public function leaseExpirySoon(string $phone, string $tenantName, string $unitName, string $expiryDate, int $daysLeft, ?int $userId = null): bool
    {
        $message = "Dear {$tenantName}, your lease for {$unitName} expires on {$expiryDate} ({$daysLeft} days). Contact your manager to renew. -MAKAZIHUB";
        return $this->send($phone, $message, $userId, 'lease_expiry');
    }

    public function welcomeTenant(string $phone, string $tenantName, string $unitName, string $propertyName, ?int $userId = null): bool
    {
        $message = "Welcome to MakaziHub, {$tenantName}! Your tenancy at {$unitName}, {$propertyName} has been set up. You can track invoices & payments via the tenant portal. -MAKAZIHUB";
        return $this->send($phone, $message, $userId, 'welcome');
    }

    // ── Helpers ────────────────────────────────────────────────────

    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);
        // Convert 07XX → +2547XX
        if (preg_match('/^0(\d{9})$/', $phone, $m)) {
            return '+254' . $m[1];
        }
        // Already E.164
        if (str_starts_with($phone, '+')) {
            return $phone;
        }
        // 254XXXXXXXXX → +254XXXXXXXXX
        if (str_starts_with($phone, '254')) {
            return '+' . $phone;
        }
        return $phone;
    }

    protected function log(?int $userId, string $type, string $contact, string $message, string $channel, bool $success, string $rawResponse): void
    {
        try {
            NotificationLog::create([
                'user_id' => $userId,
                'type' => $type,
                'channel' => $channel,
                'recipient' => $contact,
                'subject' => $type,
                'message' => $message,
                'status' => $success ? 'sent' : 'failed',
                'raw_response' => $rawResponse,
                'sent_at' => $success ? now() : null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('NotificationLog write failed: ' . $e->getMessage());
        }
    }
}
