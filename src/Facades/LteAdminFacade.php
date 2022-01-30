<?php

namespace LteAdmin\Facades;

use Illuminate\Support\Facades\Facade as FacadeIlluminate;
use LteAdmin\LteAdmin;

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
