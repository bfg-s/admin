<?php

namespace Lar\LteAdmin;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as ServiceProviderIlluminate;
use Lar\LteAdmin\Core\ConfigExtensionProvider;
use Lar\LteAdmin\Core\InstallExtensionProvider;
use Lar\LteAdmin\Core\NavGroup;
use Lar\LteAdmin\Core\NavigatorExtensionProvider;
use Lar\LteAdmin\Core\PermissionsExtensionProvider;
use Lar\LteAdmin\Core\UnInstallExtensionProvider;
use Lar\LteAdmin\Interfaces\NavigateInterface;
use Lar\LteAdmin\Models\LteFunction;

class ExtendProvider extends ServiceProviderIlluminate
{
    /**
     * Extension ID name.
     * @var string
     */
    public static $name;

    /**
     * Extension call slug.
     * @var string
     */
    public static $slug;

    /**
     * Extension description.
     * @var string
     */
    public static $description = '';

    /**
     * Role list access on extension.
     * @var Collection|LteFunction[]|null
     */
    public static $roles;

    /**
     * After route to set.
     * @var null|string
     */
    public static $after;

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
     * Simple bind in app service provider.
     * @var array
     */
    protected $bind = [

    ];

    /**
     * @var string
     */
    protected $navigator = NavigatorExtensionProvider::class;

    /**
     * @var string
     */
    protected $install = InstallExtensionProvider::class;

    /**
     * @var string
     */
    protected $uninstall = UnInstallExtensionProvider::class;

    /**
     * @var string
     */
    protected $permissions = PermissionsExtensionProvider::class;

    /**
     * @var ConfigExtensionProvider|string
     */
    protected $config = ConfigExtensionProvider::class;

    /**
     * Bootstrap services.
     *
     * @return void
     * @throws \Exception
     */
    public function boot()
    {
        /** @var LteFunction $func */
        $func = gets()
            ->lte;

        if ($func) {
            $func = $func->functions->list;

            if ($func) {
                $func = $func->where('class', static::class)
                    ->where('slug', 'access')
                    ->first();
            }
        }

        if ($func) {
            static::$roles = $func->roles;
        }

        foreach ($this->bind as $key => $item) {
            if (is_numeric($key)) {
                $key = $item;
            }
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
        if (! static::$name) {
            $this->getNameAndDescription();
        }
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
     * Generate extension slug.
     */
    protected function generateSlug()
    {
        if (! static::$slug) {
            static::$slug = preg_replace('/[^A-Za-z]/', '_', static::$name);
        }

        static::$slug = preg_replace('/[^A-Za-z]/', '_', static::$slug);
    }

    /**
     * Get name and description from composer.json.
     */
    protected function getNameAndDescription()
    {
        $dir = dirname((new \ReflectionClass(static::class))->getFileName());

        $file = $dir.'/../composer.json';

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
     * Extension navigator element.
     * @param  Navigate|NavGroup|NavigateInterface  $navigate
     * @return void
     */
    public function navigator(NavigateInterface $navigate): void
    {
        if ($this->navigator) {
            (new $this->navigator($navigate, $this))->handle();
        }
    }

    /**
     * Install process.
     * @param  Command  $command
     * @return void
     */
    public function install(Command $command): void
    {
        if ($this->install) {
            (new $this->install($command, $this))->handle();
        }
    }

    /**
     * Uninstall process.
     * @param  Command  $command
     * @return void
     */
    public function uninstall(Command $command): void
    {
        if ($this->uninstall) {
            (new $this->uninstall($command, $this))->handle();
        }
    }

    /**
     * Permission process.
     * @param  Command  $command
     * @param  string  $type
     * @return void
     */
    public function permission(Command $command, string $type): void
    {
        if ($this->permissions) {
            if ($type === 'up') {
                (new $this->permissions($command, $this))->up();
            } elseif ($type === 'down') {
                (new $this->permissions($command, $this))->down();
            }
        }
    }

    /**
     * Extension configs.
     * @return ConfigExtensionProvider
     */
    public function config()
    {
        if ($this->config && is_string($this->config)) {
            $this->config = new $this->config($this);
        }

        return $this->config;
    }
}
