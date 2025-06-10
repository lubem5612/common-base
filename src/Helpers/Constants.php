<?php

namespace Transave\CommonBase\Helpers;

class Constants
{
    public const ACCOUNT_STATUS = [
        'unverified'    => 'unverified',
        'verified'      => 'verified',
        'suspended'     => 'suspended',
        'banned'        => 'banned',
        'incomplete'    => 'incomplete'
    ];

    public const ACCOUNT_TYPE = [
        'ordinary'  => 'ordinary',
        'classic'   => 'classic',
        'premium'   => 'premimum',
        'super'     => 'super'
    ];

    public const USER_ROLES = [
        'customer' => 'customer'
    ];

    public const WALLET_PREFIX = 'Transave-';

    public const CATEGORIES = [
        'BANK_TRANSFER'             => 'BANK_TRANSFER',
        'BANK_TRANSFER_COMMISSION'  => 'BANK_TRANSFER_COMMISSION',
        'BILL_PAYMENT'              => 'BILL_PAYMENT'
    ];

    public const IS_VERIFIED = [
        'yes'   => 'yes',
        'no'    => 'no'
    ];

    public const TRANSACTION_TYPE = [
        'DEBIT'     => 'debit',
        'CREDIT'    => 'credit'
    ];

    public const SUCCESSFUL = 'successful';

    public const FAILED = 'failed';

    public const PROCESSING = 'processing';

    public const PENDING = 'pending';

    public const SUCCESS_BILL_RESPONSE = 'Your request has been successfully received and is currently being processed.';
}
