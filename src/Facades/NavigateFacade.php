<?php

namespace Lar\LteAdmin\Facades;

use Illuminate\Support\Facades\Facade as FacadeIlluminate;
use Lar\LteAdmin\Navigate;

/**
 * Class Facade
 * 
 * @package Lar
 */
class NavigateFacade extends FacadeIlluminate
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Navigate::class;
    }
}
