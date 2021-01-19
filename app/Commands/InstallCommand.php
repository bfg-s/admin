<?php

namespace Admin\Commands;

use Admin\Extension\Providers\ApplicationProvider;
use Admin\Extension\Providers\ConfigProvider;
use Admin\Models\AdminSeeder;
use Admin\Models\AdminUser;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class LteUpdateAssets
 *
 * @package Lar\LteAdmin\Commands
 */
class InstallCommand extends Command
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
    protected $description = 'Install BFG admin';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('admin:update', ['--migrate' => true]);

        $make_seeds = false;

        if (!AdminUser::count()) {

            $this->call('db:seed', [
                '--class' => AdminSeeder::class
            ]);

            $make_seeds = true;
        }

        $base_dirs = ['/', '/Controllers', '/Extensions'];

        foreach ($base_dirs as $base_dir) {

            if (!is_dir($dir = admin_app_path($base_dir))) {

                mkdir($dir, 0777, true);

                $this->info("Directory {$dir} created!");
            }
        }

        $public_dirs = ['/uploads/images', 'uploads/files'];

        foreach ($public_dirs as $public_dir) {

            if (!is_dir($dir = public_path($public_dir))) {
                mkdir($dir, 0777, true);

                $this->info("Directory {$dir} created!");
            }
        }

        $this->makeApp();

        $extensions = storage_path('admin_extensions.php');

        if (!is_file($extensions)) {

            file_put_contents(
                $extensions,
                "<?php\n\nreturn [\n\t\n];"
            );

            $this->info("File {$extensions} created!");
        }

        $controller = admin_app_path('Controllers/Controller.php');

        if (!is_file($controller)) {

            file_put_contents(
                $controller,
                "<?php\n\nnamespace ".admin_app_namespace('Controllers').";\n\nuse Admin\Http\Controllers\Controller as AdminController;\n\n/**\n * Controller Class\n *\n * @package ".admin_app_namespace('Controllers')."\n */\nclass Controller extends AdminController\n{\n\t\n}"
            );

            $this->info("File {$controller} created!");
        }

        $gitignore = file_get_contents(base_path('.gitignore'));

        $add_to_ignore = "";

        if (strpos($gitignore, 'public/admin') === false) {
            $add_to_ignore .= "public/admin\n";
            $this->info("Add folder [public/admin] to .gitignore");
        }

        if ($add_to_ignore) {

            file_put_contents(base_path('.gitignore'), trim($gitignore) . "\n" . $add_to_ignore);
        }

        if ($make_seeds) {

            $this->call('lte:extension', ['--install' => true, '--yes' => true, '--force' => true]);
        }

        $this->info("Bfg Admin Installed");
    }

    /**
     * Make app classes
     */
    protected function makeApp()
    {
        $config = admin_app_path('Config.php');

        if (!is_file($config)) {

            $class = class_entity('Config');
            $class->namespace(admin_app_namespace());
            $class->wrap('php');
            $class->extend(ConfigProvider::class);
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
            $class->extend(ApplicationProvider::class);

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
