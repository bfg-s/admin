<?php

declare(strict_types=1);

namespace Admin\Facades;

use Admin\Admin;
use Illuminate\Support\Facades\Facade as FacadeIlluminate;

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
