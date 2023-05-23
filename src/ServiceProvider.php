<?php

namespace Admin;

use Blade;
use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider as ServiceProviderIlluminate;
use Illuminate\Support\Str;
use Lar\Layout\Layout;
use Lar\LJS\JaxController;
use Lar\LJS\JaxExecutor;
use Admin\Commands\AdminControllerCommand;
use Admin\Commands\AdminDbDumpCommand;
use Admin\Commands\AdminExtensionCommand;
use Admin\Commands\AdminHelpersCommand;
use Admin\Commands\AdminInstallCommand;
use Admin\Commands\AdminUserCommand;
use Admin\Core\BladeDirectiveAlpineStore;
use Admin\Exceptions\Handler;
use Admin\Layouts\AdminAuthLayout;
use Admin\Layouts\AdminLayout;
use Admin\Middlewares\Authenticate;
use Admin\Repositories\AdminRepository;
use Road;

class ServiceProvider extends ServiceProviderIlluminate
{
    /**
     * @var array
     */
    protected $commands = [
        AdminInstallCommand::class,
        AdminControllerCommand::class,
        AdminUserCommand::class,
        AdminExtensionCommand::class,
        AdminDbDumpCommand::class,
        AdminHelpersCommand::class,
    ];

    /**
     * Simple bind in app service provider.
     * @var array
     */
    protected $bind = [

    ];

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'admin-auth' => Authenticate::class,
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     * @throws Exception
     */
    public function boot()
    {
        /**
         * Register Admin Events.
         */
        foreach ($this->listen as $event => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                Event::listen($event, $listener);
            }
        }

        /**
         * Register app routes.
         */
        if (is_file(admin_app_path('routes.php'))) {
            \Lar\Roads\Facade::domain(config('admin.route.domain', ''))
                ->web()
                ->middleware(['admin-auth'])
                ->lang(config('layout.lang_mode', true))
                ->layout(config('admin.route.layout'))
                ->prefix(config('admin.route.prefix'))
                ->name(config('admin.route.name'))
                ->group(admin_app_path('routes.php'));
        }

        /**
         * Register web routes.
         */
        if (is_file(base_path('routes/admin.php'))) {
            \Lar\Roads\Facade::domain(config('admin.route.domain', ''))
                ->web()
                ->middleware(['admin-auth'])
                ->lang(config('layout.lang_mode', true))
                ->layout(config('admin.route.layout'))
                ->prefix(config('admin.route.prefix'))
                ->name(config('admin.route.name'))
                ->group(base_path('routes/admin.php'));
        }

        /**
         * Register Admin basic routes.
         */
        \Lar\Roads\Facade::domain(config('admin.route.domain', ''))
            ->web()
            ->lang(config('layout.lang_mode', true))
            ->middleware(['admin-auth'])
            ->prefix(config('admin.route.prefix'))
            ->name(config('admin.route.name'))
            ->group(__DIR__.'/routes.php');

        /**
         * Register publishers configs.
         */
        $this->publishes([
            __DIR__.'/../config/admin.php' => config_path('admin.php'),
        ], 'admin-config');

        /**
         * Register publishers lang.
         */
        $this->publishes([
            __DIR__.'/../translations/en' => lang_path('en'),
            __DIR__.'/../translations/ru' => lang_path('ru'),
            __DIR__.'/../translations/uk' => lang_path('uk'),
        ], ['admin-lang', 'laravel-assets']);

        /**
         * Register publishers assets.
         */
        $this->publishes([
            base_path('/vendor/almasaeed2010/adminlte/dist') => public_path('/admin-asset'),
            base_path('/vendor/almasaeed2010/adminlte/plugins') => public_path('/admin-asset/plugins'),
            __DIR__.'/../assets' => public_path('/admin'),
        ], ['admin-assets', 'laravel-assets']);

        /**
         * Register publishers migrations.
         */
        $this->publishes([
            __DIR__.'/../migrations' => database_path('migrations'),
        ], ['admin-migrations', 'laravel-assets']);

        /**
         * Register publishers html examples.
         */
        $this->publishes([
            base_path('/vendor/almasaeed2010/adminlte/pages') => public_path('/admin-html'),
        ], 'admin-html');

        /**
         * Load Admin views.
         */
        $this->loadViewsFrom(__DIR__.'/../views', 'admin');

        if ($this->app->runningInConsole()) {
            /**
             * Run boots.
             */
            Boot::run();
        }

        /**
         * Register repositories
         */
        $this->app->singleton(AdminRepository::class, fn() => new AdminRepository);

        /**
         * Make view variables.
         */
        $this->viewVariables();

        /**
         * Simple bind in service container.
         */
        foreach ($this->bind as $key => $item) {
            if (is_numeric($key)) {
                $key = $item;
            }
            $this->app->bind($key, $item);
        }

        /**
         * Run with jax on admin page.
         */
        JaxController::on_start(static function () {
            $ref = request()->server->get('HTTP_REFERER');
            if ($ref && Str::is(url(config('admin.route.prefix').'*'), $ref)) {
                Boot::run();
            }
        });

        /**
         * Register Jax namespace.
         */
        \Lar\LJS\Facade::jaxNamespace(admin_relative_path('Jax'), admin_app_namespace('Jax'));

        /**
         * Register AlpineJs Blade directive.
         */
        Blade::directive('alpineStore', [BladeDirectiveAlpineStore::class, 'directive']);
    }

    /**
     * Make view variables.
     */
    private function viewVariables()
    {
        app('view')->share([
            'admin' => config('admin'),
            'default_page' => config('admin.paths.view', 'admin').'.page'
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     * @throws Exception
     */
    public function register()
    {
        $this->app->singleton(Page::class, function ($app) {
            return new Page($app->router);
        });

        /**
         * App register provider.
         */
        if (class_exists('App\Providers\AdminServiceProvider')) {
            $this->app->register('App\Providers\AdminServiceProvider');
        }

        /**
         * Override errors.
         */
        $this->app->singleton(
            ExceptionHandler::class,
            Handler::class
        );

        /**
         * Merge config from having by default.
         */
        $this->mergeConfigFrom(
            __DIR__.'/../config/admin.php',
            'admin'
        );

        /**
         * Register admin middleware.
         */
        $this->registerRouteMiddleware();

        /**
         * Register admin commands.
         */
        $this->commands($this->commands);

        /**
         * Setup auth and disc configuration.
         */
        $this->loadAuthAndDiscConfig();

        /**
         * Register layout.
         */
        Layout::registerComponent('admin_layout', AdminLayout::class);

        /**
         * Register Login layout.
         */
        Layout::registerComponent('admin_auth_layout', AdminAuthLayout::class);

        /**
         * Register jax executors.
         */
        $this->registerJax();

        $sqlite = config('admin.connections.admin-sqlite.database');

        if (!is_file($sqlite)) {
            file_put_contents($sqlite, '');
        }
    }

    /**
     * Register the route middleware.
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }
    }

    /**
     * Setup auth and disc configuration.
     *
     * @return void
     */
    private function loadAuthAndDiscConfig()
    {
        config(Arr::dot(config('admin.auth', []), 'auth.'));
        config(Arr::dot(config('admin.disks', []), 'filesystems.disks.'));
        config(Arr::dot(config('admin.connections', []), 'database.connections.'));
    }

    /**
     * Register jax executors.
     */
    protected function registerJax()
    {
        JaxExecutor::addNamespace(__DIR__.'/Jax', 'Admin\\Jax');
    }
}
