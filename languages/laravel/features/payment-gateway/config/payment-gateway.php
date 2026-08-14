<?php

return [
    'provider' => env('PAYMENT_GATEWAY_PROVIDER'),
    'mode' => env('PAYMENT_GATEWAY_MODE', 'pg'),
    'razorpay' => [
        'key' => env('RAZORPAY_KEY'),
        'secret' => env('RAZORPAY_SECRET'),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
    ],
    'easebuzz' => [
        'key' => env('EASEBUZZ_KEY'),
        'salt' => env('EASEBUZZ_SALT'),
        'env' => env('EASEBUZZ_ENV', 'test'),
    ],
    'phonepe' => [
        'merchant_id' => env('PHONEPE_MERCHANT_ID'),
        'salt_key' => env('PHONEPE_SALT_KEY'),
        'salt_index' => env('PHONEPE_SALT_INDEX'),
        'env' => env('PHONEPE_ENV', 'test'),
    ],
];
