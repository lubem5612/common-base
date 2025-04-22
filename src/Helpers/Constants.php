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
}
