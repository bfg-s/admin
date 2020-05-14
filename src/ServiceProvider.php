<?php

namespace Lar\LteAdmin;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as ServiceProviderIlluminate;
use Lar\Developer\Commands\DumpAutoload;
use Lar\Layout\Executor;
use Lar\Layout\Layout;
use Lar\LteAdmin\Commands\LteInstall;
use Lar\LteAdmin\Commands\MakeController;
use Lar\LteAdmin\Core\BladeBootstrap;
use Lar\LteAdmin\Core\Generators\ExtensionNavigatorHelperGenerator;
use Lar\LteAdmin\Core\Generators\FunctionsHelperGenerator;
use Lar\LteAdmin\Exceptions\Handler;
use Lar\LteAdmin\Middlewares\Authenticate;

/**
 * Class ServiceProvider
 *
 * @package Lar\Layout
 */
class ServiceProvider extends ServiceProviderIlluminate
{
    /**
     * @var array
     */
    protected $commands = [
        LteInstall::class,
        MakeController::class
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'lte-auth' => Authenticate::class
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     * @throws \Exception
     */
    public function boot()
    {
        /**
         * Register component group
         */
        //Tag::$groups['lte'] = LteGroup::class;

        /**
         * Register app routes
         */
        if (is_file(lte_app_path('routes.php'))) {

            \Road::domain(config('lte.route.domain', ''))
                ->web()
                ->middleware(['lte-auth'])
                ->lang(config('layout.lang_mode', true))
                ->gets('lte')
                ->layout(config('lte.route.layout'))
                ->namespace(config('lte.route.namespace'))
                ->prefix(config('lte.route.prefix'))
                ->name(config('lte.route.name'))
                ->group(lte_app_path('routes.php'));
        }

        /**
         * Register web routes
         */
        if (is_file(base_path('routes/admin.php'))) {

            \Road::domain(config('lte.route.domain', ''))
                ->web()
                ->middleware(['lte-auth'])
                ->lang(config('layout.lang_mode', true))
                ->gets('lte')
                ->layout(config('lte.route.layout'))
                ->namespace(config('lte.route.namespace'))
                ->prefix(config('lte.route.prefix'))
                ->name(config('lte.route.name'))
                ->group(base_path('routes/admin.php'));
        }

        /**
         * Register Lte Admin basic routes
         */
        \Road::domain(config('lte.route.domain', ''))
            ->web()
            ->lang(config('layout.lang_mode', true))
            ->gets('lte')
            ->middleware(['lte-auth'])
            ->prefix(config('lte.route.prefix'))
            ->name(config('lte.route.name'))
            ->group(__DIR__ . '/routes.php');

        /**
         * Register publishers configs
         */
        $this->publishes([
            __DIR__.'/../views/default' => resource_path('views/admin')
        ], 'lte-view');

        /**
         * Register publishers configs
         */
        $this->publishes([
            __DIR__.'/../config/lte.php' => config_path('lte.php')
        ], 'lte-config');

        /**
         * Register publishers assets
         */
        $this->publishes([
            base_path('/vendor/almasaeed2010/adminlte/dist') => public_path('/lte-asset'),
            base_path('/vendor/almasaeed2010/adminlte/plugins') => public_path('/lte-asset/plugins'),
            __DIR__ . '/../assets' => public_path('/lte-admin'),
        ], 'lte-assets');

        /**
         * Register publishers adminlte assets
         */
        $this->publishes([
            base_path('/vendor/almasaeed2010/adminlte/dist') => public_path('/lte-asset'),
            base_path('/vendor/almasaeed2010/adminlte/plugins') => public_path('/lte-asset/plugins'),
        ], 'lte-adminlte-assets');

        /**
         * Register publishers migrations
         */
        $this->publishes([
            __DIR__.'/../migrations' => database_path('migrations'),
        ], 'lte-migrations');

        /**
         * Register publishers html examples
         */
        $this->publishes([
            base_path('/vendor/almasaeed2010/adminlte/pages') => public_path('/lte-html'),
        ], 'lte-html');

        /**
         * Load AdminLte views
         */
        $this->loadViewsFrom(__DIR__.'/../views', 'lte');

        /**
         * Load AdminLte Translations
         */
        $this->loadTranslationsFrom(__DIR__.'/../translations', 'lte');

        /**
         * Register blade bootstrap directives
         */
        BladeBootstrap::run();

        if ($this->app->runningInConsole()) {

            /**
             * Register lte admin getter for console
             */
            \Get::create('lte');
        }

        /**
         * Make lte view variables
         */
        $this->viewVariables();

        /**
         * Register getters
         */
        \Get::register(\Lar\LteAdmin\Getters\Menu::class);
        \Get::register(\Lar\LteAdmin\Getters\Role::class);
    }

    /**
     * Register services.
     *
     * @return void
     * @throws \Exception
     */
    public function register()
    {
        /**
         * Override errors
         */
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            Handler::class
        );

        /**
         * Helper registration
         */
        DumpAutoload::addToExecute(FunctionsHelperGenerator::class);
        DumpAutoload::addToExecute(ExtensionNavigatorHelperGenerator::class);

        /**
         * Merge config from having by default
         */
        $this->mergeConfigFrom(
            __DIR__.'/../config/lte.php', 'lte'
        );

        /**
         * Register Lte middleware
         */
        $this->registerRouteMiddleware();

        /**
         * Register Lte commands
         */
        $this->commands($this->commands);

        /**
         * Setup auth and disc configuration.
         */
        $this->loadAuthAndDiscConfig();

        /**
         * Register Lte layout
         */
        Layout::registerComponent("lte_layout", \Lar\LteAdmin\Layouts\LteLayout::class);

        /**
         * Register Lte Login layout
         */
        Layout::registerComponent("lte_auth_layout", \Lar\LteAdmin\Layouts\LteAuthLayout::class);

        /**
         * Register lte jax executors
         */
        $this->registerJax();
    }

    /**
     * Register jax executors
     */
    protected function registerJax()
    {
        Executor::addExecutor(\Lar\LteAdmin\Jax\LteAdmin::class);
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
     * Setup auth and disc configuration.
     *
     * @return void
     */
    private function loadAuthAndDiscConfig()
    {
        config(\Arr::dot(config('lte.auth', []), 'auth.'));
        config(\Arr::dot(config('lte.disks', []), 'filesystems.disks.'));
    }

    /**
     * Make lte view variables
     */
    private function viewVariables()
    {
        app('view')->share([
            'lte' => config('lte'),
            'default_page' => config('lte.paths.view', 'admin').'.page'
        ]);

        Collection::macro('nestable_pluck', function (string $value, string $key, $root = 'Root', string $order = "order", string $parent_field = "parent_id", string $input = "&nbsp;&nbsp;&nbsp;") {

            $nestable_count = function ($parent_id) use ($parent_field, &$nestable_count) {

                $int = 1;
                $parent = $this->where('id', $parent_id)->first();
                if ($parent->{$parent_field}) { $int += $nestable_count($parent->{$parent_field}); }
                return $int;
            };

            /** @var Collection $return */
            $return = $this->sortBy($order)->mapWithKeys(function ($item) use ($value, $key, $parent_field, $input, $nestable_count){

                $inp_cnt = 0;
                if ($item->{$parent_field}) { $inp_cnt += $nestable_count($item->{$parent_field}); }
                return [$item->{$key} => str_repeat($input, $inp_cnt) . $item->{$value}];
            });

            if ($root) {

                $return->prepend($root, 0);
            }

            return $return;
        });
    }
}

