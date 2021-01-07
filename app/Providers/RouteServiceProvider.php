<?php

namespace Admin\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class RouteServiceProvider
 * @package Admin\Providers
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * The application's route middleware.
     * @var array
     */
    protected $routeMiddleware = [

    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        // Register the route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {

            app('router')->aliasMiddleware($key, $middleware);
        }
    }
}

