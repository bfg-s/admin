<?php

namespace Bfg\Admin;

use Illuminate\Support\Facades\Facade as FacadeIlluminate;

/**
 * Class Facade
 * @package Bfg\Admin
 */
class Facade extends FacadeIlluminate
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Admin::class;
    }
}
