<?php

namespace Raadaa\RaadaaBase;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Raadaa\RaadaaBase\Skeleton\SkeletonClass
 */
class RaadaaBaseFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'raadaa-base';
    }
}
