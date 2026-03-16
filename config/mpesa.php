<?php
// config/mpesa.php
// Add these to your .env file:
//
// MPESA_CONSUMER_KEY=your_consumer_key
// MPESA_CONSUMER_SECRET=your_consumer_secret
// MPESA_PAYBILL=174379
// MPESA_PASSKEY=your_passkey
// MPESA_CALLBACK_URL=https://yourdomain.com/mpesa/callback
// MPESA_SANDBOX=true

return [
    'consumer_key'    => env('MPESA_CONSUMER_KEY'),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
    'paybill'         => env('MPESA_PAYBILL', '174379'),     // 174379 = Safaricom sandbox
    'passkey'         => env('MPESA_PASSKEY'),
    'callback_url'    => env('MPESA_CALLBACK_URL'),
    'sandbox'         => env('MPESA_SANDBOX', true),
];
