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

    'constants' => [
        'create_virtual_account'        => 'ADMIN_CREATE_VIRTUAL_ACCOUNT',
        'list_virtual_accounts'         => 'ADMIN_VIRTUAL_ACCOUNTS',
        'update_virtual_account'        => 'ADMIN_UPDATE_VIRTUAL_ACCOUNT',
        'disable_virtual_account'       => 'ADMIN_DISABLE_VIRTUAL_ACCOUNT',
        'enable_virtual_account'        => 'ADMIN_ENABLE_VIRTUAL_ACCOUNT',
        'get_single_virtual_account'    => 'ADMIN_RETRIEVE_SINGLE_VIRTUAL_ACCOUNT',
        'bank_list'                     => 'BANK_LIST',
        'name_inquiry'                  => 'NAME_ENQUIRY',

        'single_fund_transfer'          => 'SINGLE_FUND_TRANSFER',
        'virtual_account_fund_transfer' => 'VIRTUAL_ACCOUNT_FUND_TRANSFER',
        'fund_virtual_account'          => 'FUND_VIRTUAL_ACCOUNT',
        'withdraw_virtual_account'      => 'WITHDRAW_VIRTUAL_ACCOUNT',
        'get_virtual_account_balance'   => 'RETRIEVE_VIRTUAL_ACCOUNT_BALANCE',
        'update_virtual_account_limit'  => 'UPDATE_VIRTUAL_ACCOUNT_LIMIT',

        'transaction'  => [
            'status'                    => 'TRANSACTION_STATUS_QUERY',
            'main_account'              => 'ADMIN_MAIN_ACCOUNT_TRANSACTIONS',
            'main_account_filter'       => 'ADMIN_MAIN_ACCOUNT_FILTERED_TRANSACTIONS',
            'virtual_account'           => 'ADMIN_VIRTUAL_ACCOUNT_TRANSACTIONS',
            'virtual_account_filter'    => 'ADMIN_VIRTUAL_ACCOUNT_FILTERED_TRANSACTIONS',
        ],
    ],
];
