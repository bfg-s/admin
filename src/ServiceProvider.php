<?php

namespace LteAdmin;

use Arr;
use Blade;
use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider as ServiceProviderIlluminate;
use Lar\Layout\Layout;
use Lar\LJS\JaxController;
use Lar\LJS\JaxExecutor;
use LJS;
use LteAdmin\Commands\LteControllerCommand;
use LteAdmin\Commands\LteDbDumpCommand;
use LteAdmin\Commands\LteExtensionCommand;
use LteAdmin\Commands\LteHelpersCommand;
use LteAdmin\Commands\LteInstallCommand;
use LteAdmin\Commands\LteUserCommand;
use LteAdmin\Core\BladeDirectiveAlpineStore;
use LteAdmin\Exceptions\Handler;
use LteAdmin\Layouts\LteAuthLayout;
use LteAdmin\Layouts\LteLayout;
use LteAdmin\Middlewares\Authenticate;
use LteAdmin\Repositories\AdminRepository;
use Road;
use Str;

class ServiceProvider extends ServiceProviderIlluminate
{
    /**
     * @var array
     */
    protected $commands = [
        LteInstallCommand::class,
        LteControllerCommand::class,
        LteUserCommand::class,
        LteExtensionCommand::class,
        LteDbDumpCommand::class,
        LteHelpersCommand::class,
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
        'lte-auth' => Authenticate::class,
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
         * Register AdminLte Events.
         */
        foreach ($this->listen as $event => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                Event::listen($event, $listener);
            }
        }

        /**
         * Register app routes.
         */
        if (is_file(lte_app_path('routes.php'))) {
            Road::domain(config('lte.route.domain', ''))
                ->web()
                ->middleware(['lte-auth'])
                ->lang(config('layout.lang_mode', true))
                ->layout(config('lte.route.layout'))
                ->prefix(config('lte.route.prefix'))
                ->name(config('lte.route.name'))
                ->group(lte_app_path('routes.php'));
        }

        /**
         * Register web routes.
         */
        if (is_file(base_path('routes/admin.php'))) {
            Road::domain(config('lte.route.domain', ''))
                ->web()
                ->middleware(['lte-auth'])
                ->lang(config('layout.lang_mode', true))
                ->layout(config('lte.route.layout'))
                ->prefix(config('lte.route.prefix'))
                ->name(config('lte.route.name'))
                ->group(base_path('routes/admin.php'));
        }

        /**
         * Register Lte Admin basic routes.
         */
        Road::domain(config('lte.route.domain', ''))
            ->web()
            ->lang(config('layout.lang_mode', true))
            ->middleware(['lte-auth'])
            ->prefix(config('lte.route.prefix'))
            ->name(config('lte.route.name'))
            ->group(__DIR__.'/routes.php');

        /**
         * Register publishers configs.
         */
        $this->publishes([
            __DIR__.'/../config/lte.php' => config_path('lte.php'),
        ], 'lte-config');

        /**
         * Register publishers lang.
         */
        $this->publishes([
            __DIR__.'/../translations/en' => resource_path('lang/en'),
            __DIR__.'/../translations/ru' => resource_path('lang/ru'),
            __DIR__.'/../translations/uk' => resource_path('lang/uk'),
        ], ['lte-lang', 'laravel-assets']);

        /**
         * Register publishers assets.
         */
        $this->publishes([
            base_path('/vendor/almasaeed2010/adminlte/dist') => public_path('/lte-asset'),
            base_path('/vendor/almasaeed2010/adminlte/plugins') => public_path('/lte-asset/plugins'),
            __DIR__.'/../assets' => public_path('/lte-admin'),
        ], ['lte-assets', 'laravel-assets']);

        /**
         * Register publishers adminlte assets.
         */
        $this->publishes([
            base_path('/vendor/almasaeed2010/adminlte/dist') => public_path('/lte-asset'),
            base_path('/vendor/almasaeed2010/adminlte/plugins') => public_path('/lte-asset/plugins'),
        ], ['lte-adminlte-assets', 'laravel-assets']);

        /**
         * Register publishers migrations.
         */
        $this->publishes([
            __DIR__.'/../migrations' => database_path('migrations'),
        ], ['lte-migrations', 'laravel-assets']);

        /**
         * Register publishers html examples.
         */
        $this->publishes([
            base_path('/vendor/almasaeed2010/adminlte/pages') => public_path('/lte-html'),
        ], 'lte-html');

        /**
         * Load AdminLte views.
         */
        $this->loadViewsFrom(__DIR__.'/../views', 'lte');

        if ($this->app->runningInConsole()) {
            /**
             * Run lte boots.
             */
            LteBoot::run();
        }

        /**
         * Register repositories
         */
        $this->app->singleton(AdminRepository::class, fn() => new AdminRepository);

        /**
         * Make lte view variables.
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
         * Run lte with jax on admin page.
         */
        JaxController::on_start(static function () {
            $ref = request()->server->get('HTTP_REFERER');
            if ($ref && Str::is(url(config('lte.route.prefix').'*'), $ref)) {
                LteBoot::run();
            }
        });

        /**
         * Register Jax namespace.
         */
        LJS::jaxNamespace(lte_relative_path('Jax'), lte_app_namespace('Jax'));

        /**
         * Register AlpineJs Blade directive.
         */
        Blade::directive('alpineStore', [BladeDirectiveAlpineStore::class, 'directive']);
    }

    /**
     * Make lte view variables.
     */
    private function viewVariables()
    {
        app('view')->share([
            'lte' => config('lte'),
            'default_page' => config('lte.paths.view', 'admin').'.page'
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
        if (!$this->app->runningUnitTests()) {
            if (class_exists('App\Providers\AdminServiceProvider')) {
                $this->app->register('App\Providers\AdminServiceProvider');
            }
        } else {
            $this->app->register('LteAdmin\Tests\Providers\AdminServiceProvider');
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
            __DIR__.'/../config/lte.php',
            'lte'
        );

        /**
         * Register Lte middleware.
         */
        $this->registerRouteMiddleware();

        /**
         * Register Lte commands.
         */
        $this->commands($this->commands);

        /**
         * Setup auth and disc configuration.
         */
        $this->loadAuthAndDiscConfig();

        /**
         * Register Lte layout.
         */
        Layout::registerComponent('lte_layout', LteLayout::class);

        /**
         * Register Lte Login layout.
         */
        Layout::registerComponent('lte_auth_layout', LteAuthLayout::class);

        /**
         * Register lte jax executors.
         */
        $this->registerJax();

        $sqlite = config('lte.connections.lte-sqlite.database');

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
        config(Arr::dot(config('lte.auth', []), 'auth.'));
        config(Arr::dot(config('lte.disks', []), 'filesystems.disks.'));
        config(Arr::dot(config('lte.connections', []), 'database.connections.'));
    }

    /**
     * Register jax executors.
     */
    protected function registerJax()
    {
        JaxExecutor::addNamespace(__DIR__.'/Jax', 'LteAdmin\\Jax');
    }
}
