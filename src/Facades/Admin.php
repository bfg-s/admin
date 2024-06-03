<?php

declare(strict_types=1);

namespace Admin\Facades;

use Admin\AdminEngine;
use Illuminate\Support\Facades\Facade as FacadeIlluminate;

/**
 * Admin panel core facade.
 *
 * @mixin AdminEngine
 */
class Admin extends FacadeIlluminate
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return AdminEngine::class;
    }
}
