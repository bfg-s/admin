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
     * @var bool
     */
    static $installed = false;

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
        /**
         * Admin installed flag
         */
        static::$installed = is_file(storage_path('admin_extensions.php'));

        /**
         * Merge configs
         */
        $this->mergeAdminConfigs();

        /**
         * Merge config from having by default
         */
        $this->mergeConfigFrom(
            __DIR__.'/../../config/admin.php', 'admin'
        );

        /**
         * Exit if not installed
         */
        if (!static::$installed) {

            return ;
        }

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
        if (class_exists($provider = config('admin.provider'))) {

            $this->app->register($provider);
        }
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

