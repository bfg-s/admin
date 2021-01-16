<?php

namespace Admin\Http\Middleware;

use Bfg\Layout\Middleware\LayoutMiddleware;

/**
 * Class Layout
 * @package Admin\Http\Middleware
 */
class Layout extends LayoutMiddleware
{
    /**
     * Internal override for middleware children
     * @param  string  $sign
     * @return bool
     */
    protected function checkClass(string $sign)
    {
        return config('admin.route.layout');
    }
}
