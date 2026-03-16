<?php
// config/africastalking.php

return [
    /*
    |----------------------------------------------------------------------
    | Africa's Talking Credentials
    |----------------------------------------------------------------------
    | Get your API key and username from https://africastalking.com
    | For sandbox testing use username "sandbox" and the sandbox API key.
    */
    'api_key'   => env('AT_API_KEY', ''),
    'username'  => env('AT_USERNAME', 'sandbox'),
    'sender_id' => env('AT_SENDER_ID', 'MAKAZIHUB'),  // Must be registered with AT
    'sandbox'   => env('AT_SANDBOX', true),
];
