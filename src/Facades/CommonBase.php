<?php

namespace Transave\CommonBase\Facades;

use Illuminate\Support\Facades\Facade;

class CommonBase extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'commonbase';
    }
}
