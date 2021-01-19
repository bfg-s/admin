<?php

namespace Admin\Providers;

use Admin\Commands\ExtensionCommand;
use Admin\Commands\InstallCommand;
use Admin\Commands\UpdateCommand;
use Admin\Dumps\ModelsHelperDump;
use Admin\Models\AdminFileStorage;
use Admin\Models\AdminPermission;
use Admin\Models\AdminRole;
use Admin\Models\AdminUser;
use Admin\Models\AdminUserPermission;
use Bfg\Dev\Commands\BfgDumpCommand;
use Bfg\Dev\Commands\DumpAutoload;
use Illuminate\Support\ServiceProvider;

/**
 * Class ConsoleServiceProvider
 * @package Admin\Providers
 */
class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Admin Console commands
     * @var array
     */
    protected $commands = [
        InstallCommand::class,
        UpdateCommand::class,
        ExtensionCommand::class,
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     * @throws \Exception
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {

            $this->runningInConsole();
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Register services for console.
     * @return void
     */
    protected function runningInConsole(): void
    {
        /**
         * Launch of all services and extensions of the admin panel.
         */
        \AdminExtension::boot();

        /**
         * Register admin commands
         */
        $this->commands($this->commands);

        /**
         * Register publisher admin configs
         */
        $this->publishes([
            __DIR__.'/../../config/admin.php' => config_path('admin.php'),
        ], 'admin-config');

        /**
         * Register publisher admin assets
         */
        $this->publishes([
            __DIR__.'/../../public' => admin_path_asset(),
        ], 'admin-assets');

        /**
         * Register publisher admin language files
         */
        $this->publishes([
            __DIR__.'/../../resources/lang' => resource_path('lang'),
        ], 'admin-lang');

        /**
         * Register publisher admin migrations
         */
        $this->publishes([
            __DIR__.'/../../database/migrations' => database_path('/migrations'),
        ], 'admin-migrations');

        $this->toDumpAutoLoad();

        $this->toBfgDump();
    }

    /**
     * Register dump autoload admin classes
     * @return void
     */
    protected function toDumpAutoLoad(): void
    {
        DumpAutoload::addToExecute(ModelsHelperDump::class);
    }

    /**
     * Add admin models to seed dump
     * @return void
     */
    protected function toBfgDump(): void
    {
        BfgDumpCommand::addModel(AdminFileStorage::class);
        BfgDumpCommand::addModel(AdminPermission::class);
        BfgDumpCommand::addModel(AdminRole::class);
        BfgDumpCommand::addModel(AdminUser::class);
        BfgDumpCommand::addModel(AdminUserPermission::class);
    }
}

