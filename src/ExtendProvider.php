<?php

namespace Admin;

use Admin\Facades\AdminFacade;
use Admin\Models\AdminMenu;
use Admin\Models\AdminSetting;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider as ServiceProviderIlluminate;
use Admin\Core\ConfigExtensionProvider;
use Admin\Core\InstallExtensionProvider;
use Admin\Core\NavGroup;
use Admin\Core\NavigatorExtensionProvider;
use Admin\Core\PermissionsExtensionProvider;
use Admin\Core\UnInstallExtensionProvider;
use Admin\Interfaces\NavigateInterface;
use Admin\Models\LteFunction;
use ReflectionClass;

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
     * @throws Exception
     */
    public function boot()
    {
        foreach ($this->bind as $key => $item) {
            if (is_numeric($key)) {
                $key = $item;
            }
            $this->app->bind($key, $item);
        }

        $db = config('admin.connections.admin-sqlite.database');

        if (is_file($db) && Schema::connection('admin-sqlite')->hasTable('admin_settings')) {
            AdminSetting::get(['name', 'value'])->map(
                fn (AdminSetting $setting) => Config::set($setting->name, $setting->value)
            );
        }
    }

    /**
     * Register services.
     *
     * @return void
     * @throws Exception
     */
    public function register()
    {
        if (!static::$name) {
            $this->getNameAndDescription();
        }
        $this->generateSlug();
        $this->registerRouteMiddleware();
        $this->commands($this->commands);
        AdminFacade::registerExtension($this);
    }

    /**
     * Get name and description from composer.json.
     */
    protected function getNameAndDescription()
    {
        $dir = dirname((new ReflectionClass(static::class))->getFileName());

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
     * Generate extension slug.
     */
    protected function generateSlug()
    {
        if (!static::$slug) {
            static::$slug = preg_replace('/[^A-Za-z]/', '_', static::$name);
        }

        static::$slug = preg_replace('/[^A-Za-z]/', '_', static::$slug);
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
     * @return bool
     */
    public function included()
    {
        return isset(Admin::$extensions[static::$name]) && Admin::$extensions[static::$name];
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
