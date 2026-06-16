<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Midtrans Credentials
    |--------------------------------------------------------------------------
    |
    | Server Key and Client Key from your Midtrans dashboard.
    | Sandbox: https://dashboard.sandbox.midtrans.com
    | Production: https://dashboard.midtrans.com
    |
    */

    'server_key' => env('MIDTRANS_SERVER_KEY'),

    'client_key' => env('MIDTRANS_CLIENT_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Production Mode
    |--------------------------------------------------------------------------
    |
    | Set to true to accept real transactions.
    | When false, the sandbox environment is used.
    |
    */

    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),

];
