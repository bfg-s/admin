<?php

namespace Lar\LteAdmin\Facades;

use Illuminate\Support\Facades\Facade as FacadeIlluminate;
use Lar\LteAdmin\LteAdmin;

/**
 * Class Facade
 * 
 * @package Lar
 */
class LteAdminFacade extends FacadeIlluminate
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return LteAdmin::class;
    }
}
