<?php

namespace Admin\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class RunServiceProvider
 * @package Admin\Providers
 */
class RunServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     * @throws \Exception
     */
    public function boot()
    {
        if (\Admin::installed()) {

            foreach (\AdminExtension::extensions() as $extension) {

                if ($extension->included()) {

                    $extension->config()->boot();
                }
            }
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if (\Admin::installed()) {

            foreach (\AdminExtension::extensions() as $extension) {

                if ($extension->included()) {

                    $extension->config()->register();
                }
            }
        }
    }
}