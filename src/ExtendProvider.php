<?php

declare(strict_types=1);

namespace Admin;

use Admin\Core\ConfigExtensionProvider;
use Admin\Core\InstallExtensionProvider;
use Admin\Core\NavigatorExtensionProvider;
use Admin\Core\UnInstallExtensionProvider;
use Admin\Facades\Admin;
use Admin\Interfaces\NavigateInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider as ServiceProviderIlluminate;
use ReflectionClass;

/**
 * Class for extending the extension provider.
 */
class ExtendProvider extends ServiceProviderIlluminate
{
    /**
     * Extension ID name.
     *
     * @var string
     */
    public static string $name;

    /**
     * Extension call slug.
     *
     * @var string
     */
    public static string $slug;

    /**
     * Extension description.
     *
     * @var string
     */
    public static string $description = '';

    /**
     * Set extension routes after all other extensions.
     *
     * @var string|null
     */
    public static string|null $after = null;

    /**
     * Built-in expansion commands.
     *
     * @var array
     */
    protected array $commands = [

    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected array $routeMiddleware = [

    ];

    /**
     * Simple bind in app service provider.
     *
     * @var array
     */
    protected array $bind = [

    ];

    /**
     * Navigator class for extension.
     *
     * @var string
     */
    protected string $navigator = NavigatorExtensionProvider::class;

    /**
     * The installer class for the extension.
     *
     * @var string
     */
    protected string $install = InstallExtensionProvider::class;

    /**
     * Removal class for extension.
     *
     * @var string
     */
    protected string $uninstall = UnInstallExtensionProvider::class;

    /**
     * Extension configuration class.
     *
     * @var ConfigExtensionProvider|string
     */
    protected ConfigExtensionProvider|string $config = ConfigExtensionProvider::class;

    /**
     * Method for initializing the configuration. Called when the configuration is loaded.
     *
     * @return void
     * @throws Exception
     */
    public function boot(): void
    {
        foreach ($this->bind as $key => $item) {
            if (is_numeric($key)) {
                $key = $item;
            }
            $this->app->bind($key, $item);
        }
    }

    /**
     * A method that is executed immediately after registering a service provider.
     *
     * @return void
     * @throws Exception
     */
    public function register(): void
    {
        if (!static::$name) {
            $this->getNameAndDescription();
        }
        $this->generateSlug();
        $this->registerRouteMiddleware();
        $this->commands($this->commands);
        Admin::registerExtension($this);
    }

    /**
     * Get name and description from composer.json.
     *
     * @return void
     */
    protected function getNameAndDescription(): void
    {
        $dir = dirname((new ReflectionClass(static::class))->getFileName());

        $file = $dir.'/../composer.json';

        if (is_file($file)) {
            $data = json_decode(file_get_contents($file), true);

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
     *
     * @return void
     */
    protected function generateSlug(): void
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
    protected function registerRouteMiddleware(): void
    {
        foreach ($this->routeMiddleware as $key => $middleware) {

            app('router')->aliasMiddleware($key, $middleware);
        }
    }

    /**
     * A method that determines whether the extension will participate in the navigation of the admin panel.
     *
     * @return bool
     */
    public function included(): bool
    {
        return isset(AdminEngine::$extensions[static::$name]) && AdminEngine::$extensions[static::$name];
    }

    /**
     * Extension navigator element.
     *
     * @param  NavigateInterface  $navigate
     * @return void
     */
    public function navigator(NavigateInterface $navigate): void
    {
        if ($this->navigator) {
            (new $this->navigator($navigate, $this))->handle();
        }
    }

    /**
     * Extension install process.
     *
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
     * Extension uninstall process.
     *
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
     * Get extension config class.
     *
     * @return ConfigExtensionProvider|string
     */
    public function config(): ConfigExtensionProvider|string
    {
        if ($this->config && is_string($this->config)) {
            $this->config = new $this->config($this);
        }

        return $this->config;
    }
}
