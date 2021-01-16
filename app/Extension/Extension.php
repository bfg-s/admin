<?php

namespace Admin\Extension;

use Admin\Extension\Providers\ConfigProvider;
use Admin\Extension\Providers\InstallProvider;
use Admin\Extension\Providers\UnInstallProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Command;

/**
 * Class Extension
 * @package Admin\Extension
 */
class Extension extends ServiceProvider
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
     * Simple bind in app service provider
     * @var array
     */
    protected $bind = [

    ];

    /**
     * @var string
     */
    protected $install = InstallProvider::class;

    /**
     * @var string
     */
    protected $uninstall = UnInstallProvider::class;

    /**
     * @var ConfigProvider|string
     */
    protected $config = ConfigProvider::class;

    /**
     * Bootstrap services.
     *
     * @return void
     * @throws \Exception
     */
    public function boot()
    {
        foreach ($this->bind as $key => $item) {
            if (is_numeric($key)) $key = $item;
            $this->app->bind($key, $item);
        }
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
        \Admin::registerExtension($this);
    }

    /**
     * @return bool
     */
    public function included()
    {
        return !!\Admin::extension(static::$name);
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

            static::$slug = preg_replace('/[^A-Za-z]/', '_', static::$name);
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
     * Install process
     * @param  Command  $command
     * @return void
     */
    public function install(Command $command): void {

        if ($this->install) {

            (new $this->install($command, $this))->handle();
        }
    }

    /**
     * Uninstall process
     * @param  Command  $command
     * @return void
     */
    public function uninstall(Command $command): void {

        if ($this->uninstall) {

            (new $this->uninstall($command, $this))->handle();
        }
    }

    /**
     * Extension configs
     * @return ConfigProvider
     */
    public function config() {

        if ($this->config && is_string($this->config)) {

            $this->config = new $this->config($this);
        }

        return $this->config;
    }
}

