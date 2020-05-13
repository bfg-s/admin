<?php

namespace Lar\LteAdmin;

use Illuminate\Support\ServiceProvider as ServiceProviderIlluminate;
use Lar\LteAdmin\Interfaces\NavigateInterface;
use Lar\LteAdmin\Core\NavGroup;
use Illuminate\Console\Command;

/**
 * Class ServiceProvider
 *
 * @package Lar\Layout
 */
abstract class ExtendProvider extends ServiceProviderIlluminate
{
    /**
     * @var string
     */
    static $name;

    /**
     * @var array
     */
    protected $commands = [

    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [

    ];

    /**
     * Bootstrap services.
     *
     * @return void
     * @throws \Exception
     */
    public function boot()
    {

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRouteMiddleware();
        $this->commands($this->commands);
        \LteAdmin::registerExtension($this);
    }

    /**
     * Register the route middleware.
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {

            app('router')->aliasMiddleware($key, $middleware);
        }
    }

    /**
     * Extension navigator element
     * @param  Navigate|NavGroup|NavigateInterface  $navigate
     * @return void
     */
    abstract public function navigator(NavigateInterface $navigate): void;

    /**
     * Install process
     * @param  Command  $command
     * @return void
     */
    abstract public function install(Command $command): void;
}

