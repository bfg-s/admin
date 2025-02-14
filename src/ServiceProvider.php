<?php

declare(strict_types=1);

namespace Admin;

use Admin\BladeDirectives\AlpineStoreBladeDirective;
use Admin\BladeDirectives\AttributeRealtimeBladeDirective;
use Admin\BladeDirectives\AttributesBladeDirective;
use Admin\BladeDirectives\SystemCssBladeDirective;
use Admin\BladeDirectives\SystemJsBladeDirective;
use Admin\BladeDirectives\SystemJsVariablesBladeDirective;
use Admin\BladeDirectives\SystemMetasBladeDirective;
use Admin\BladeDirectives\SystemScriptsBladeDirective;
use Admin\BladeDirectives\SystemStylesBladeDirective;
use Admin\BladeDirectives\UpdateWithPjaxBladeDirective;
use Admin\Commands\AdminControllerCommand;
use Admin\Commands\AdminExtensionCommand;
use Admin\Commands\AdminHelpersCommand;
use Admin\Commands\AdminInstallCommand;
use Admin\Commands\AdminKeyCommand;
use Admin\Commands\AdminUserCommand;
use Admin\Controllers\AuthController;
use Admin\Facades\Admin;
use Admin\Middlewares\ApiMiddleware;
use Admin\Middlewares\Authenticate;
use Admin\Middlewares\BrowserDetectMiddleware;
use Admin\Middlewares\DomMiddleware;
use Admin\Middlewares\LanguageMiddleware;
use Admin\Models\AdminUser;
use Admin\Observers\AdminUserObserver;
use Admin\Repositories\AdminRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider as ServiceProviderIlluminate;
use Laravel\Dusk\DuskServiceProvider;
use ReflectionException;

/**
 * The main class of the service provider of the admin panel
 * in which all additional features of the admin panel are connected,
 * such as publishing assets, declaring commands, middleware, and so on.
 */
class ServiceProvider extends ServiceProviderIlluminate
{
    /**
     * List of admin panel commands.
     *
     * @var array
     */
    protected array $commands = [
        AdminInstallCommand::class,
        AdminControllerCommand::class,
        AdminUserCommand::class,
        AdminExtensionCommand::class,
        AdminHelpersCommand::class,
        AdminKeyCommand::class,
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
     * @throws ReflectionException
     */
    public function boot(): void
    {
        URL::defaults(['adminLang' => Admin::nowLang()]);

        /**
         * Register app routes.
         */
        if (is_file(admin_app_path('routes.php'))) {
            $this->makeRouter()->group(admin_app_path('routes.php'));
        }

        /**
         * Register web routes.
         */
        if (is_file(base_path('routes/admin.php'))) {
            $this->makeRouter()->group(base_path('routes/admin.php'));
        }

        /**
         * Register Admin basic routes.
         */
        $this->makeRouter()->group(__DIR__.'/routes.php');

        /**
         * Create info route.
         */
        $this->makeRouter(false)
            ->get('bfg/info', [AuthController::class, 'info'])
            ->name('info');

        $routerForExtensions = $this->makeRouter();

        /**
         * Register extensions routes
         */
        foreach (Admin::extensions() as $extension) {
            $extension->config()->routes($routerForExtensions);
        }

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
            __DIR__.'/../translations/uk' => lang_path('uk'),
        ], ['admin-lang']);

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
        ], ['admin-migrations']);

        /**
         * Register publishers html examples.
         */
        $this->publishes([
            base_path('/vendor/almasaeed2010/adminlte/pages') => public_path('/admin-html'),
        ], 'admin-html');

        /**
         * Load themes views
         */
        foreach (Admin::getThemes() as $theme) {
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
        Blade::directive('realtime', [AttributeRealtimeBladeDirective::class, 'directive']);
        Blade::directive('adminSystemJs', [SystemJsBladeDirective::class, 'directive']);
        Blade::directive('adminSystemJsVariables', [SystemJsVariablesBladeDirective::class, 'directive']);
        Blade::directive('adminSystemCss', [SystemCssBladeDirective::class, 'directive']);
        Blade::directive('adminSystemScripts', [SystemScriptsBladeDirective::class, 'directive']);
        Blade::directive('adminSystemStyles', [SystemStylesBladeDirective::class, 'directive']);
        Blade::directive('adminSystemMetas', [SystemMetasBladeDirective::class, 'directive']);
        Blade::directive('updateWithPjax', [UpdateWithPjaxBladeDirective::class, 'directive']);

        /**
         * Register local respond class
         */
        $this->app->instance(Respond::class, Respond::glob());

        /**
         * Register model observers
         */
        AdminUser::observe(AdminUserObserver::class);
    }

    /**
     * Create a router admin panel.
     *
     * @param  bool  $prefix
     * @return RouteRegistrar
     */
    protected function makeRouter(bool $prefix = true): RouteRegistrar
    {
        $route = Route::domain(config('admin.route.domain', ''))
            ->name(config('admin.route.name'));

        $middlewares = [
            \Illuminate\Session\Middleware\StartSession::class,
            ApiMiddleware::class,
            'web',
        ];

        if ($prefix) {
            if (config('admin.lang_mode', true)) {
                $route = $route->prefix('{adminLang}/'.config('admin.route.prefix'))
                    ->where(['adminLang' => implode('|', Admin::getLangs())]);
                $middlewares[] = LanguageMiddleware::class;
            } else {
                $route = $route->prefix(config('admin.route.prefix'));
            }
        }

        $middlewares[] = 'admin-auth';
        $middlewares[] = DomMiddleware::class;
        $middlewares[] = BrowserDetectMiddleware::class;

        return $route->middleware($middlewares);
    }

    /**
     * Create a router for redirecting from a language for the admin panel.
     *
     * @return void
     */
    public function redirectebleRoute(): void
    {
        if (config('admin.lang_mode', true)) {
            Route::domain(config('admin.route.domain', ''))
                ->name(config('admin.route.name').'index')
                ->prefix(config('admin.route.prefix'))
                ->middleware(['web', 'admin-auth', DomMiddleware::class, LanguageMiddleware::class])
                ->get('/', function (Request $request) {
                    return redirect()->route(config('admin.home-route', 'admin.dashboard'));
                });
        }
    }

    /**
     * Make view variables.
     *
     * @return void
     */
    private function viewVariables(): void
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
    public function register(): void
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

        /**
         * Register duck test class
         */
        if (
            $this->app->environment('local', 'testing')
            && class_exists(DuskServiceProvider::class)
        ) {
            $this->app->register(DuskServiceProvider::class);
        }
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
     * Setup auth and disc configuration.
     *
     * @return void
     */
    private function loadAuthAndDiscConfig(): void
    {
        config(Arr::dot(config('admin.auth', []), 'auth.'));
        config(Arr::dot(config('admin.disks', []), 'filesystems.disks.'));
    }
}
