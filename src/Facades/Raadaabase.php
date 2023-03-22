<?php

namespace Raadaapartners\Raadaabase\Facades;

use Illuminate\Support\Facades\Facade;

class Raadaabase extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'raadaabase';
    }
}
