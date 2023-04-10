<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    /*
     |
     | SendChamp SMS service and transactional mails configurations
     |
     */
    'sendchamp' => [
        'username'      => env('SENDCHAMP_USERNAME', 'Sendchamp'),
        'route'         => env('SENDCHAMP_ROUTE', 'dnd'),
        'public_key'    => env('SENDCHAMP_PUBLIC_KEY', ''),
    ],

    /*
     |
     | Termii SMS service configurations
     |
     */
    'termii' => [
        'username'      => env('TERMII_USERNAME', ''),
        'message_type'  => env('TERMII_MESSAGE_TYPE', 'plain'),
        'channel'       => env('TERMII_MESSAGE_CHANNEL', 'dnd'),
        'api_key'       => env('TERMII_API_KEY', ''),
    ],

    /*
     |
     | Kuda Account variables
     |
     */
    'kuda' => [
        'email'         => env('KUDA_EMAIL', ''),
        'base_url'      => env('KUDA_BASE_URL', 'https://kuda-openapi-uat.kudabank.com/v​2.1'),
        'api_key'       => env('KUDA_API_KEY', ''),
        'acc_number'    => env('KUDA_ACCOUNT_NUMBER', ''),
        'acc_name'      => env('KUDA_ACCOUNT_NAME', ''),
        'webhook_url'   => env('KUDA_WEBHOOK_URL', ''),
    ],

    /*
     |
     | Paystack merchant configurations
     |
     */
    'paystack' => [
        'secret_key'    => env('PAYSTACK_SECRET_KEY', ''),
        'public_key'    => env('PAYSTACK_PUBLIC_KEY', ''),
        'callback_url'  => env('PAYSTACK_CALLBACK_URL', ''),
        'base_url'      => env('PAYSTACK_BASE_URL', ''),
    ],

    /*
     | Azure storage Url
     |
     | These is the base url to files stored with azure storage blob
     */
    'azure' => [
        'storage_url' => 'https://'.env('AZURE_STORAGE_NAME').'.blob.core.windows.net/'.env('AZURE_STORAGE_CONTAINER').'/',
    ],

];
