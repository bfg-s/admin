<?php

namespace Admin;

use Admin\BladeDirectives\AlpineStoreBladeDirective;
use Admin\BladeDirectives\AttributesBladeDirective;
use Admin\BladeDirectives\SystemCssBladeDirective;
use Admin\BladeDirectives\SystemJsBladeDirective;
use Admin\BladeDirectives\SystemJsVariablesBladeDirective;
use Admin\BladeDirectives\SystemScriptsBladeDirective;
use Admin\BladeDirectives\SystemStylesBladeDirective;
use Admin\BladeDirectives\UpdateWithPjaxBladeDirective;
use Admin\Commands\AdminControllerCommand;
use Admin\Commands\AdminExtensionCommand;
use Admin\Commands\AdminHelpersCommand;
use Admin\Commands\AdminInstallCommand;
use Admin\Commands\AdminUserCommand;
use Admin\Facades\AdminFacade;
use Admin\Middlewares\Authenticate;
use Admin\Middlewares\DomMiddleware;
use Admin\Middlewares\LanguageMiddleware;
use Admin\Repositories\AdminRepository;
use Exception;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as ServiceProviderIlluminate;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;

class ServiceProvider extends ServiceProviderIlluminate
{
    /**
     * @var array
     */
    protected array $commands = [
        AdminInstallCommand::class,
        AdminControllerCommand::class,
        AdminUserCommand::class,
        AdminExtensionCommand::class,
        AdminHelpersCommand::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected array $routeMiddleware = [
        'admin-auth' => Authenticate::class,
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function boot(): void
    {
        /**
         * Register app routes.
         */
        if (is_file(admin_app_path('routes.php'))) {
            $this->makeRouters()->group(admin_app_path('routes.php'));
        }

        /**
         * Register web routes.
         */
        if (is_file(base_path('routes/admin.php'))) {
            $this->makeRouters()->group(base_path('routes/admin.php'));
        }

        /**
         * Register Admin basic routes.
         */
        $this->makeRouters()->group(__DIR__.'/routes.php');

        /**
         * Register redirecteble route
         */
        $this->redirectebleRoute();

        /**
         * Register publishers configs.
         */
        $this->publishes([
            __DIR__.'/../config/admin.php' => config_path('admin.php'),
        ], ['admin-config']);

        /**
         * Register publishers default theme.
         */
        $this->publishes([
            __DIR__.'/../lte' => public_path('vendor/admin/lte'),
        ], ['admin-theme']);

        /**
         * Register publishers lang.
         */
        $this->publishes([
            __DIR__.'/../translations/en' => lang_path('en'),
            __DIR__.'/../translations/ru' => lang_path('ru'),
            __DIR__.'/../translations/ua' => lang_path('ua'),
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
         * Load themes views
         */
        foreach (AdminFacade::getThemes() as $theme) {
            if (
                ($namespace = $theme->getNamespace())
                && ($directory = $theme->getDirectory())
            ) {
                $this->loadViewsFrom($directory, $namespace);
            }
        }

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
         * Register Blade directives.
         */
        Blade::directive('alpineStore', [AlpineStoreBladeDirective::class, 'directive']);
        Blade::directive('attributes', [AttributesBladeDirective::class, 'directive']);
        Blade::directive('adminSystemJs', [SystemJsBladeDirective::class, 'directive']);
        Blade::directive('adminSystemJsVariables', [SystemJsVariablesBladeDirective::class, 'directive']);
        Blade::directive('adminSystemCss', [SystemCssBladeDirective::class, 'directive']);
        Blade::directive('adminSystemScripts', [SystemScriptsBladeDirective::class, 'directive']);
        Blade::directive('adminSystemStyles', [SystemStylesBladeDirective::class, 'directive']);
        Blade::directive('updateWithPjax', [UpdateWithPjaxBladeDirective::class, 'directive']);

        /**
         * Register local respond class
         */
        $this->app->instance(Respond::class, Respond::glob());
    }

    /**
     * @return RouteRegistrar
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function makeRouters(): \Illuminate\Routing\RouteRegistrar
    {
        $route = Route::domain(config('admin.route.domain', ''))
            ->name(config('admin.route.name'));

        $middlewares = ['web', 'admin-auth', DomMiddleware::class];

        if (config('admin.lang_mode', true)) {
            $route = $route->prefix(AdminFacade::nowLang() . '/' . config('admin.route.prefix'));
            $middlewares[] = LanguageMiddleware::class;
        } else {
            $route = $route->prefix(config('admin.route.prefix'));
        }

        return $route->middleware($middlewares);
    }

    /**
     * @return void
     */
    public function redirectebleRoute()
    {
        if (config('admin.lang_mode', true)) {
            Route::domain(config('admin.route.domain', ''))
                ->name(config('admin.route.name') . 'index')
                ->prefix(config('admin.route.prefix'))
                ->middleware(['web', 'admin-auth', DomMiddleware::class, LanguageMiddleware::class])
                ->get('/', function () {
                    return redirect()->route(config('admin.home-route', 'admin.dashboard'));
                });
        }
    }

    /**
     * Make view variables.
     */
    private function viewVariables()
    {
        app('view')->share([
            'admin' => config('admin'),
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
    }
}
