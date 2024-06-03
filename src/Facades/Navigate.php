<?php

declare(strict_types=1);

namespace Admin\Facades;

use Admin\NavigateEngine;
use Illuminate\Support\Facades\Facade as FacadeIlluminate;

/**
 * Navigation core facade of the admin panel.
 */
class Navigate extends FacadeIlluminate
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return NavigateEngine::class;
    }
}
