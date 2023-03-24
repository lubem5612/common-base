<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'sendchamp' => [
        'username'      => env('SENDCHAMP_USERNAME', 'Sendchamp'),
        'route'         => env('SENDCHAMP_ROUTE', 'dnd'),
        'public_key'    => env('SENDCHAMP_PUBLIC_KEY', ''),
    ],

    'termii' => [
        'username'      => env('TERMII_USERNAME', ''),
        'message_type'  => env('TERMII_MESSAGE_TYPE', 'plain'),
        'channel'       => env('TERMII_MESSAGE_CHANNEL', 'dnd'),
        'api_key'       => env('TERMII_API_KEY', ''),
    ],

    'kuda' => [
        'email'         => env('KUDA_EMAIL', ''),
        'base_url'      => env('KUDA_BASE_URL', ''),
        'api_key'       => env('KUDA_API_KEY', ''),
        'webhook_url'   => env('KUDA_WEBHOOK_URL', ''),
    ],

];
