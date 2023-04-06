<?php

namespace Raadaapartners\Raadaabase;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Raadaapartners\Raadaabase\Skeleton\SkeletonClass
 */
class RaadaabaseFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'raadaabase';
    }
}
