<?php

namespace Admin\Providers;

use Admin\Exceptions\Handler;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider
 * @package Admin\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     * @throws \Exception
     */
    public function boot()
    {
        /**
         * Load Admin views
         */
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'admin');

        /**
         * Load component views namespace
         */
        Blade::componentNamespace('Admin\\Components', 'app');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeAdminConfigs();

        /**
         * Override errors
         */
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            Handler::class
        );

        /**
         * Register application provider if exists
         */
        if (class_exists(\App\Providers\AdminServiceProvider::class)) {

            $this->app->register(\App\Providers\AdminServiceProvider::class);
        }

        /**
         * Merge config from having by default
         */
        $this->mergeConfigFrom(
            __DIR__.'/../../config/admin.php', 'admin'
        );
    }

    /**
     * Setup auth and disc configuration.
     *
     * @return void
     */
    protected function mergeAdminConfigs()
    {
        config(\Arr::dot(config('admin.auth', []), 'auth.'));
        config(\Arr::dot(config('admin.disks', []), 'filesystems.disks.'));
    }
}

