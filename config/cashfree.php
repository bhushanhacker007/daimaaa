<?php

return [
    'environment' => env('CASHFREE_ENVIRONMENT', 'sandbox'), // sandbox or production

    // Verification Suite credentials
    'verification' => [
        'client_id' => env('CASHFREE_VRS_CLIENT_ID', ''),
        'client_secret' => env('CASHFREE_VRS_CLIENT_SECRET', ''),
    ],

    // Payouts credentials
    'payout' => [
        'client_id' => env('CASHFREE_PAYOUT_CLIENT_ID', ''),
        'client_secret' => env('CASHFREE_PAYOUT_CLIENT_SECRET', ''),
    ],

    'webhook_secret' => env('CASHFREE_WEBHOOK_SECRET', ''),
];
