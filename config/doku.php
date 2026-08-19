<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DOKU SNAP Credentials
    |--------------------------------------------------------------------------
    |
    | Configuration for DOKU SNAP B2B Direct API integration (QRIS).
    |
    */

    'client_id' => env('DOKU_CLIENT_ID'),

    'secret_key' => env('DOKU_SECRET_KEY'),

    // RSA Private Key (PEM format, PKCS#8 or PKCS#1)
    'private_key' => env('DOKU_PRIVATE_KEY'),

    'merchant_id' => env('DOKU_MERCHANT_ID'),

    'terminal_id' => env('DOKU_TERMINAL_ID', 'TERMINAL01'),

    'postal_code' => env('DOKU_POSTAL_CODE', '10110'),

    'is_production' => (bool) env('DOKU_IS_PRODUCTION', false),

    'sandbox_base_url' => env('DOKU_SANDBOX_BASE_URL', 'https://api-sandbox.doku.com'),

    'production_base_url' => env('DOKU_PRODUCTION_BASE_URL', 'https://api.doku.com'),

];
