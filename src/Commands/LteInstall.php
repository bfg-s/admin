<?php

namespace Lar\LteAdmin\Commands;

use Composer\Json\JsonFormatter;
use Illuminate\Console\Command;
use Lar\LteAdmin\Models\LteSeeder;
use Lar\LteAdmin\Models\LteUser;

/**
 * Class LteUpdateAssets
 *
 * @package Lar\LteAdmin\Commands
 */
class LteInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lte:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install or update admin LTE';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('vendor:publish', [
            '--tag' => 'lte-migrations',
            '--force' => true
        ]);

        $make_seeds = false;

        if (!\Schema::hasTable('lte_users')) {

            $make_seeds = true;
        }

        else if (!LteUser::count()) {

            $make_seeds = true;
        }

        $this->call('migrate', array_filter([
            '--force' => true
        ]));

        if ($make_seeds) {

            $this->call('db:seed', [
                '--class' => LteSeeder::class
            ]);
        }

        if (!is_dir($dir = lte_app_path())) {

            mkdir($dir, 0777, true);

            $this->info("Directory {$dir} created!");
        }

        if (!is_dir($dir = lte_app_path('Controllers'))) {

            mkdir($dir, 0777, true);

            $this->info("Directory {$dir} created!");
        }

        if (!is_dir($dir = lte_app_path('Extensions'))) {

            mkdir($dir, 0777, true);

            $this->info("Directory {$dir} created!");
        }

        if (!is_dir($dir = public_path('uploads'))) {

            mkdir($dir, 0777, true);

            $this->info("Directory {$dir} created!");
        }

        if (!is_dir($dir = public_path('uploads/images'))) {

            mkdir($dir, 0777, true);

            $this->info("Directory {$dir} created!");
        }

        if (!is_dir($dir = public_path('uploads/files'))) {

            mkdir($dir, 0777, true);

            $this->info("Directory {$dir} created!");
        }

        if (!is_dir($dir = resource_path("views/admin"))) {

            mkdir($dir, 0777, true);

            $this->info("Directory {$dir} created!");
        }

        $this->call('vendor:publish', [
            '--tag' => 'lte-view',
            '--force' => true
        ]);

        $nav = lte_app_path('navigator.php');

        if (!is_file($nav)) {

            file_put_contents(
                $nav,
                "<?php\n\nuse Lar\Roads\Roads;\nuse Lar\LteAdmin\Navigate;\nuse Lar\LteAdmin\Core\NavGroup;\n\nNavigate::do(function (Navigate \$navigate, Roads \$roads) {\n\t\n});"
            );

            $this->info("File {$nav} created!");
        }

        $bootstrap = lte_app_path('bootstrap.php');

        if (!is_file($bootstrap)) {

            file_put_contents(
                $bootstrap,
                "<?php\n\nuse \Lar\Layout\Respond;\nuse Lar\Layout\Tags\TABLE;\n\n"
            );

            $this->info("File {$bootstrap} created!");
        }

        $controller = lte_app_path('Controllers/Controller.php');

        if (!is_file($controller)) {

            file_put_contents(
                $controller,
                "<?php\n\nnamespace App\LteAdmin\Controllers;\n\nuse Lar\LteAdmin\Controllers\Controller as LteController;\n\n/**\n * Controller Class\n *\n * @package App\LteAdmin\Controllers\n */\nclass Controller extends LteController\n{\n\t\n}"
            );

            $this->info("File {$controller} created!");
        }

        $this->call('vendor:publish', [
            '--tag' => 'ljs-assets',
            '--force' => true
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'lte-assets',
            '--force' => true
        ]);

        if (!is_file(config_path('layout.php'))) {

            $this->call('vendor:publish', [
                '--tag' => 'lar-layout-config'
            ]);
        }

        if (!is_file(config_path('lte.php'))) {

            $this->call('vendor:publish', [
                '--tag' => 'lte-config'
            ]);
        }

        $base_composer = json_decode(file_get_contents(base_path('composer.json')), 1);

        if (!isset($base_composer['scripts']['post-autoload-dump']) || array_search('@php artisan lar:dump', $base_composer['scripts']['post-autoload-dump']) === false) {

            $base_composer['scripts']['post-autoload-dump'][] = 'chmod -R 0777 public/uploads';
            $base_composer['scripts']['post-autoload-dump'][] = '@php artisan lar:dump';

            file_put_contents(base_path('composer.json'), JsonFormatter::format(json_encode($base_composer), false, true));

            $this->info("File composer.json updated!");
        }

        $gitignore = file_get_contents(base_path('.gitignore'));

        $add_to_ignore = "";

        if (strpos($gitignore, 'public/lte-asset') === false) {
            $add_to_ignore .= "public/lte-asset\n";
            $this->info("Add folder [public/lte-asset] to .gitignore");
        }

        if (strpos($gitignore, 'public/lte-admin') === false) {
            $add_to_ignore .= "public/lte-admin\n";
            $this->info("Add folder [public/lte-admin] to .gitignore");
        }

        if (strpos($gitignore, 'public/ljs') === false) {
            $add_to_ignore .= "public/ljs\n";
            $this->info("Add folder [public/ljs] to .gitignore");
        }

        if ($add_to_ignore) {

            file_put_contents(base_path('.gitignore'), trim($gitignore) . "\n" . $add_to_ignore);
        }


        $this->info("Lar Admin LTE Installed");
    }
}
