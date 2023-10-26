<?php

namespace Admin\Facades;

use Illuminate\Support\Facades\Facade as FacadeIlluminate;
use Admin\Admin;

/**
 * @mixin Admin
 */
class AdminFacade extends FacadeIlluminate
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Admin::class;
    }
}
