<?php

namespace Admin\Commands;

use Admin\ApplicationConfig;
use App\Admin\Delegates\CommonTrait;
use File;
use Illuminate\Console\Command;
use Admin\ApplicationServiceProvider;
use Admin\Core\JsonFormatter;
use Admin\Core\NavigatorExtensionProvider;
use Admin\Interfaces\ActionWorkExtensionInterface;
use Admin\Models\AdminSeeder;
use Admin\Models\AdminUser;
use Schema;
use Symfony\Component\Console\Input\InputOption;

class AdminInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'admin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install or update BFG admin';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('vendor:publish', [
            '--tag' => 'admin-migrations',
            '--force' => true,
        ]);

        $this->call('migrate', array_filter([
            '--force' => true,
        ]));

        if ($this->option('migrate')) {
            return 0;
        }

        $make_seeds = false;

        if (!Schema::hasTable('admin_users')) {
            $make_seeds = true;
        } elseif (!AdminUser::count()) {
            $make_seeds = true;
        }

        if ($make_seeds) {
            $this->call('db:seed', [
                '--class' => AdminSeeder::class,
            ]);
        }

        $base_dirs = [
            '/',
            '/Controllers',
            //'/Delegates'
        ];

        foreach ($base_dirs as $base_dir) {
            if (!is_dir($dir = admin_app_path($base_dir))) {
                mkdir($dir, 0777, true);

                $this->info("Directory {$dir} created!");
            }
        }

        $public_dirs = ['/uploads/images', 'uploads/files'];

        foreach ($public_dirs as $public_dir) {
            if (!is_dir($dir = public_path($public_dir))) {
                @mkdir($dir, 0777, true);

                $this->info("Directory {$dir} created!");
            }
        }

        $this->makeApp();

        $extensions = app()->bootstrapPath('admin_extensions.php');

        if (!is_file($extensions)) {
            file_put_contents(
                $extensions,
                "<?php\n\nreturn [\n\t\n];"
            );

            $this->info("File {$extensions} created!");

            $base_composer = json_decode(file_get_contents(base_path('composer.json')), 1);

            if (
                !isset($base_composer['scripts']['post-autoload-dump'])
                || !in_array('@php artisan admin:helpers', $base_composer['scripts']['post-autoload-dump'])
            ) {
                $base_composer['scripts']['post-autoload-dump'][] = '@php artisan admin:helpers';

                file_put_contents(
                    base_path('composer.json'),
                    JsonFormatter::format(json_encode($base_composer), false, true)
                );

                $this->info('File composer.json updated!');
            }

            $gitignore = file_get_contents(base_path('.gitignore'));

            $add_to_ignore = '';

            if (!str_contains($gitignore, 'public/admin-asset')) {
                $add_to_ignore .= "public/admin-asset\n";
                $this->info('Add folder [public/admin-asset] to .gitignore');
            }

            if (!str_contains($gitignore, 'public/admin')) {
                $add_to_ignore .= "public/admin\n";
                $this->info('Add folder [public/admin] to .gitignore');
            }

            if ($add_to_ignore) {
                file_put_contents(base_path('.gitignore'), trim($gitignore)."\n".$add_to_ignore);
            }
        }

        $controller = admin_app_path('Controllers/Controller.php');

        if (!is_file($controller)) {
            file_put_contents(
                $controller,
                "<?php\n\nnamespace ".admin_app_namespace('Controllers').";\n\nuse Admin\Controllers\Controller as AdminController;\n\nclass Controller extends AdminController\n{\n\t\n}"
            );

            $this->info("File {$controller} created!");
        }

//        $delegates = admin_app_path('Delegates');
//
//        $currentDelegates = File::allFiles(__DIR__.'/../Delegates');
//
//        if (!trait_exists(CommonTrait::class)) {
//            $file = admin_app_path('Delegates/CommonTrait.php');
//            $pageClass = class_entity('CommonTrait')->traitObject();
//            $pageClass->namespace(admin_app_namespace('Delegates'));
//            $pageClass->doc(function ($doc) {
//            });
//            file_put_contents($file, $pageClass->wrap('php')->render());
//            $this->info('Common delegate created!');
//        }
//
//        foreach ($currentDelegates as $currentDelegate) {
//            $file = $delegates.'/'.$currentDelegate->getFilename();
//            if (!is_file($file)) {
//                $parentClass = class_in_file($currentDelegate->getPathname());
//                $class = class_basename($parentClass);
//                $delegateClass = class_entity($class);
//                $delegateClass->namespace(admin_app_namespace('Delegates'));
//                $delegateClass->use("$parentClass as Admin$class");
//                $delegateClass->extend("Admin$class");
//                $delegateClass->addTrait('CommonTrait');
//                file_put_contents($file, $delegateClass->wrap('php')->render());
//                $this->info("Delegate {$class} created!");
//            }
//        }

        $this->call('vendor:publish', [
            '--tag' => 'admin-lang',
            '--force' => $this->option('force'),
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'admin-assets',
            '--force' => $this->option('force'),
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'admin-assets',
            '--force' => $this->option('force'),
        ]);

        if (!is_file(config_path('admin.php'))) {
            $this->call('vendor:publish', [
                '--tag' => 'admin-config',
            ]);
        }

        if ($make_seeds) {
            $this->call('admin:extension', ['--reinstall' => true, '--yes' => true, '--force' => true]);
        }

        $this->info('Bfg Admin Installed');

        return 0;
    }

    /**
     * Make app classes.
     */
    protected function makeApp()
    {
        $nav = admin_app_path('Navigator.php');

        if (!is_file($nav)) {
            $class = class_entity('Navigator');
            $class->namespace(admin_app_namespace());
            $class->wrap('php');
            $class->extend(NavigatorExtensionProvider::class);
            $class->implement(ActionWorkExtensionInterface::class);

            $class->method('handle')
                ->returnType('void')
                ->line('$this->makeDefaults();')
                ->line()
                ->line('$this->makeExtensions();');

            file_put_contents(
                $nav,
                $class->render()
            );

            $this->info("Navigator {$nav} created!");
        }

        $config = admin_app_path('Config.php');

        if (!is_file($config)) {
            $class = class_entity('Config');
            $class->namespace(admin_app_namespace());
            $class->wrap('php');
            $class->extend(ApplicationConfig::class);
            $class->method('boot')
                ->line('parent::boot();')
                ->line()
                ->line('//');

            file_put_contents(
                $config,
                $class->render()
            );

            $this->info("Config {$config} created!");
        }

        $provider = app_path('Providers/AdminServiceProvider.php');

        if (!is_file($provider)) {
            $class = class_entity('AdminServiceProvider');
            $class->namespace('App\Providers');
            $class->wrap('php');
            $class->use(admin_app_namespace('Config'));
            $class->use(admin_app_namespace('Navigator'));
            $class->extend(ApplicationServiceProvider::class);

            $class->prop('protected:navigator', entity('Navigator::class'));
            $class->prop('protected:config', entity('Config::class'));

            file_put_contents(
                $provider,
                $class->render()
            );

            $this->info("Provider {$provider} created!");
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Publish the assets even if already exists'],
            ['migrate', 'm', InputOption::VALUE_NONE, 'Publish and run only migrations'],
            ['extension', 'e', InputOption::VALUE_OPTIONAL, 'Run install extension'],
        ];
    }
}
