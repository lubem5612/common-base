<?php

return [
    'app_env'           => env('APP_ENV', 'testing'),
    'sendchamp' => [
        'username'      => env('SENDCHAMP_USERNAME', 'Sendchamp'),
        'route'         => env('SENDCHAMP_ROUTE', 'dnd'),
        'public_key'    => env('SENDCHAMP_PUBLIC_KEY', ''),
    ],

    'route' => [
        'middleware'    => 'api',
        'prefix'        => 'api'
    ],

    'kuda' => [
        'acc_number'        => env('KUDA_ACCOUNT_NUMBER', '3000592524'),
        'acc_name'          => env('KUDA_ACCOUNT_NAME', 'TranSave Technologies LTD'),
        'phone_number'      => env('KUDA_PHONE_NUMBER', '08037395286'),
        'base_url'          => env('KUDA_BASE_URL', 'https://kuda-openapi-uat.kudabank.com/v2.1'),
        'api_key'           => env('KUDA_API_KEY', ''),
        'email'             => env('KUDA_EMAIL', 'ezugwuchigozie1@gmail.com'),
        'webhook_url'       => env('KUDA_WEBHOOK_URL', ''),
        'bank_code'         => env('KUDA_ACCOUNT_BANK_CODE', "999129"),
    ],

    'termii' => [
        'username'      => env('TERMII_USERNAME', ''),
        'message_type'  => env('TERMII_MESSAGE_TYPE', 'plain'),
        'channel'       => env('TERMII_MESSAGE_CHANNEL', 'dnd'),
        'api_key'       => env('TERMII_API_KEY', ''),
        'base_url'      => env('TERMII_BASE_URL', 'https://api.ng.termii.com/api/sms/send'),
    ],

    'paystack' => [
        'secret_key'    => env('PAYSTACK_SECRET_KEY', ''),
        'public_key'    => env('PAYSTACK_PUBLIC_KEY', ''),
        'callback_url'  => env('PAYSTACK_CALLBACK_URL', ''),
        'base_url'      => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),
    ],

    'flutterwave' => [
        'secret_key'        => env('SECRET_KEY', ''),
        'public_key'        => env('PUBLIC_KEY', ''),
        'redirect_url'      => env('REDIRECT_URL', 'https://transave.com.ng/dashboard'),
        'base_url'          => env('FLUTTERWAVE_BASE_URL', 'https://api.flutterwave.com/v3')
    ],

    'storage' => [
        'prefix' => env('STORAGE_PREFIX','transave'),
        'driver' => env('STORAGE_DRIVER', 'local'),
    ],

    'azure' => [
        'storage_url' => 'https://'.env('AZURE_STORAGE_NAME').'.blob.core.windows.net/'.env('AZURE_STORAGE_CONTAINER'),
        'id' => '.windows.net',
    ],

    's3' => [
        'storage_url' => 'https://'.env('AWS_BUCKET').'.s3.'.env('AWS_DEFAULT_REGION').'.amazonaws.com',
        'id' => 'amazonaws.com',
    ],

    'local' => [
        'storage_url' => '',
        'id' => '',
    ],

    'withdrawal_limits' => [
        'ordinary'  => 50000,
        'classic'   => 150000,
        'premium'   => 500000,
        'super'     => 5000000,
    ],

    'monetary_keys' => [
        "amount", "commission", "charges", "client_fee_charge"
    ],

    'transaction_category' => [
        'BANK_TRANSFER', 'CARD_TRANSACTION', 'BILL_PAYMENT', 'WALLET_TRANSFER', 'REVERSAL', 'LOAN_APPROVAL', 'LOAN_REPAYMENT'
    ],

    'transaction_status' => [
        'pending', 'successful', 'failed', 'cancelled'
    ],

    'identity_type' => [
        'NIN', 'Voter Card', 'Driving License', 'International Passport',
    ],

    'residential_status' => [
        'tenant', 'squatting', 'house owner', 'family'
    ],

    'employment_status' => [
        'private', 'public', 'self-employed', 'unemployed'
    ],

    'educational_qualification' => [
        'FSLC' => 'First School Leaving Certificate',
        'SSCE' => 'Senior School Certificate',
        'ND' => 'National Diploma',
        'GII' => 'Grade II Teachers’ Certificate',
        'NCE' => 'National Certificate in Education',
        'HND' => 'Higher National Diploma',
        'B.Sc' => 'Bachelor\'s Degree',
        'DVM' => 'Doctor of Veterinary Medicine',
        'PGD' => 'Postgraduate Diploma',
        'M.Sc' => 'Master\'s Degree',
        'PhD' => 'Doctor of Philosophy',
    ]
];
