<?php
// app/Services/MpesaService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    private ?string $consumerKey;
    private ?string $consumerSecret;
    private ?string $paybill;
    private ?string $passkey;
    private ?string $callbackUrl;
    private bool $sandbox;

    public function __construct()
    {
        $this->consumerKey    = config('mpesa.consumer_key');
        $this->consumerSecret = config('mpesa.consumer_secret');
        $this->paybill        = config('mpesa.paybill');
        $this->passkey        = config('mpesa.passkey');
        $this->callbackUrl    = config('mpesa.callback_url');
        $this->sandbox        = config('mpesa.sandbox', true);
    }

    /**
     * Get OAuth token from Daraja.
     */
    public function getToken(): ?string
    {
        if (!$this->consumerKey || !$this->consumerSecret) {
            Log::error('M-Pesa credentials not configured in .env');
            return null;
        }

        $url = $this->sandbox
            ? 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
            : 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        try {
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->get($url);

            if ($response->successful()) {
                return $response->json('access_token');
            }
        } catch (\Exception $e) {
            Log::error('M-Pesa token error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Initiate STK Push to tenant's phone.
     *
     * @param  string $phone      Format: 254XXXXXXXXX
     * @param  float  $amount     Amount in KES
     * @param  string $accountRef Invoice number or unit reference
     * @param  string $description Short description
     * @return array  ['success' => bool, 'checkout_request_id' => string, 'message' => string]
     */
    public function stkPush(string $phone, float $amount, string $accountRef, string $description = 'Rent Payment'): array
    {
        if (!$this->paybill || !$this->passkey || !$this->callbackUrl) {
            return ['success' => false, 'message' => 'M-Pesa configuration incomplete. Please contact the administrator.'];
        }

        $token = $this->getToken();

        if (!$token) {
            return ['success' => false, 'message' => 'Failed to authenticate with M-Pesa.'];
        }

        $timestamp = now()->format('YmdHis');
        $password  = base64_encode($this->paybill . $this->passkey . $timestamp);
        $phone     = $this->formatPhone($phone);

        $url = $this->sandbox
            ? 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
            : 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $payload = [
            'BusinessShortCode' => $this->paybill,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => 'CustomerPayBillOnline',
            'Amount'            => (int) ceil($amount),
            'PartyA'            => $phone,
            'PartyB'            => $this->paybill,
            'PhoneNumber'       => $phone,
            'CallBackURL'       => $this->callbackUrl,
            'AccountReference'  => $accountRef,
            'TransactionDesc'   => $description,
        ];

        try {
            $response = Http::withToken($token)->post($url, $payload);
            $data     = $response->json();

            Log::info('M-Pesa STK Push response', $data);

            if (isset($data['CheckoutRequestID'])) {
                return [
                    'success'             => true,
                    'checkout_request_id' => $data['CheckoutRequestID'],
                    'message'             => 'STK Push sent. Ask tenant to check their phone.',
                ];
            }

            return [
                'success' => false,
                'message' => $data['errorMessage'] ?? 'STK Push failed. Please try again.',
            ];

        } catch (\Exception $e) {
            Log::error('M-Pesa STK Push error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Network error. Please try again.'];
        }
    }

    /**
     * Handle M-Pesa callback from Daraja.
     * Called by MpesaCallbackController.
     */
    public function handleCallback(array $data): array
    {
        $body = $data['Body']['stkCallback'] ?? [];

        if (($body['ResultCode'] ?? -1) !== 0) {
            return ['success' => false, 'message' => $body['ResultDesc'] ?? 'Payment failed.'];
        }

        $items   = collect($body['CallbackMetadata']['Item'] ?? []);
        $extract = fn(string $name) => $items->firstWhere('Name', $name)['Value'] ?? null;

        return [
            'success'        => true,
            'amount'         => $extract('Amount'),
            'mpesa_receipt'  => $extract('MpesaReceiptNumber'),
            'phone_number'   => $extract('PhoneNumber'),
            'transaction_date' => $extract('TransactionDate'),
        ];
    }

    /**
     * Normalize phone to 254XXXXXXXXX format.
     */
    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '254')) {
            $phone = '254' . $phone;
        }

        return $phone;
    }
}
