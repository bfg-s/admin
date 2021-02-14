<?php

namespace Admin\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Class RouteServiceProvider
 * @package Admin\Providers
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        if (\Admin::installed()) {

            Route::find(
                __DIR__ . '/../Http/Controllers',
                Route::middleware(['web', 'admin', 'admin_layout'])
                    ->as(config('admin.route.name'))
                    ->prefix(config('admin.route.prefix'))
            );

//                ->middleware(['web', 'admin', 'admin_layout'])
//                ->as(config('admin.route.name'))->prefix(config('admin.route.prefix'));

            $this->app->call(function () {

                $app_route = base_path('routes/admin.php');

                if (is_file($app_route)) {

                    Route::middleware(['web', 'admin', 'admin_layout'])
                        ->as(config('admin.route.name'))->prefix(config('admin.route.prefix'))
                        ->group($app_route);
                }

                Route::middleware(['web', 'admin', 'admin_layout'])
                    ->as(config('admin.route.name'))->prefix(config('admin.route.prefix'))
                    ->group(__DIR__.'/../../routes/web.php');
            });
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register the route middleware.
        foreach (config('admin.route.middlewares') as $key => $middleware) {

            app('router')->aliasMiddleware($key, $middleware);
        }
    }
}

