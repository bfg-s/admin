<?php

declare(strict_types=1);

namespace Admin\Facades;

use Illuminate\Support\Facades\Facade as FacadeIlluminate;
use Admin\Navigate;

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
