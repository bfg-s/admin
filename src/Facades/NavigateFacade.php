<?php

declare(strict_types=1);

namespace Admin\Facades;

use Admin\Navigate;
use Illuminate\Support\Facades\Facade as FacadeIlluminate;

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
