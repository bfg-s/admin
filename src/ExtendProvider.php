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
     * Extension ID name
     * @var string
     */
    static $name;

    /**
     * @var string
     */
    static $description = "";

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
     * @param  null  $dir
     * @return void
     * @throws \Exception
     */
    public function register()
    {
        if (!static::$name) { $this->getNameAndDescription(); }
        $this->registerRouteMiddleware();
        $this->commands($this->commands);
        \LteAdmin::registerExtension($this);
    }

    /**
     * @return bool
     */
    public function included()
    {
        return isset(LteAdmin::$extensions[static::$name]) && LteAdmin::$extensions[static::$name];
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
     * Get name and description from composer.json
     * @param  null  $dir
     */
    protected function getNameAndDescription()
    {
        $dir = dirname((new \ReflectionClass(static::class))->getFileName());

        $file = $dir . '/../composer.json';

        if (is_file($file)) {

            $data = json_decode(file_get_contents($file), 1);

            if (isset($data['name'])) {

                static::$name = $data['name'];
            }

            if (isset($data['description'])) {

                static::$description = $data['description'];
            }
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

    /**
     * Uninstall process
     * @param  Command  $command
     */
    abstract public function uninstall(Command $command): void;
}

