<?php

namespace LteAdmin\Facades;

use Illuminate\Support\Facades\Facade as FacadeIlluminate;
use LteAdmin\Navigate;

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
