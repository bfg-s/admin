<?php

namespace Lar\LteAdmin\Facades;

use Illuminate\Support\Facades\Facade as FacadeIlluminate;
use Lar\LteAdmin\LteAdmin;

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
