<?php

namespace Admin\Extension;

use Admin\Extension\Providers\KernelProvider;
use Admin\Extension\Providers\InstallProvider;
use Admin\Extension\Providers\UnInstallProvider;
use Admin\Extension\Providers\UpdateProvider;
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
    protected $update = UpdateProvider::class;

    /**
     * @var string
     */
    protected $uninstall = UnInstallProvider::class;

    /**
     * @var KernelProvider|string
     */
    protected $config = KernelProvider::class;

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
        \AdminExtension::register($this);
    }

    /**
     * @return bool
     */
    public function included()
    {
        return !!\AdminExtension::isIncluded(static::$name);
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

            app($this->install, ['command' => $command, 'provider' => $this])->handle();
        }
    }

    /**
     * Install process
     * @param  Command  $command
     * @return void
     */
    public function update(Command $command): void {

        if ($this->update) {

            app($this->update, ['command' => $command, 'provider' => $this])->handle();
        }
    }

    /**
     * Uninstall process
     * @param  Command  $command
     * @return void
     */
    public function uninstall(Command $command): void {

        if ($this->uninstall) {

            app($this->uninstall, ['command' => $command, 'provider' => $this])->handle();
        }
    }

    /**
     * Extension configs
     * @return KernelProvider
     */
    public function config() {

        if ($this->config && is_string($this->config)) {

            $this->config = app($this->config, ['provider' => $this]);
        }

        return $this->config;
    }
}

