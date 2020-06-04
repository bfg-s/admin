<?php

namespace Lar\LteAdmin;

use Illuminate\Support\ServiceProvider as ServiceProviderIlluminate;
use Lar\LteAdmin\Core\ConfigExtensionProvider;
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
     * Extension call slug
     * @var string
     */
    static $slug;

    /**
     * Extension description
     * @var string
     */
    static $description = "";

    /**
     * @var ConfigExtensionProvider
     */
    static $config;

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
        $this->generateSlug();
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
     * @return ConfigExtensionProvider
     */
    public function cfg()
    {
        return static::$config;
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
     * Generate extension slug
     */
    protected function generateSlug() {

        if (!static::$slug) {

            static::$slug = static::$name;
        }

        static::$slug = preg_replace('/[^A-Za-z]/', '_', static::$slug);
    }

    /**
     * Get name and description from composer.json
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
     * @return void
     */
    abstract public function uninstall(Command $command): void;

    /**
     * Permission process
     * @param  Command  $command
     * @param  string  $type
     * @return void
     */
    abstract public function permission(Command $command, string $type): void;

    /**
     * Extension configs
     * @return void
     */
    abstract public function config(): void;
}

