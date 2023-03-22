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

    'kuda' => [
        'webhook_url' => env('KUDA_WEBHOOK_URL', ''),
    ],

];
