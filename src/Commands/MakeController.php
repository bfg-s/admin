<?php

namespace Lar\LteAdmin\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Lar\EntityCarrier\Core\Entities\DocumentorEntity;
use Lar\LteAdmin\Controllers\Controller;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class LteUpdateAssets
 *
 * @package Lar\LteAdmin\Commands
 */
class MakeController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'lte:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make Admin LTE Controller';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

        $ex = explode("/", $name);

        $add_dir = "";

        $namespace = config('lte.route.namespace');

        if (count($ex) > 1) {

            $a = str_replace($ex[count($ex)-1], '', $name);

            $add_dir = '/' . $a;

            $namespace .= '\\' . trim(str_replace('/', '\\', $a), '\\');

            $name = $ex[count($ex)-1];
        }

        $dir = lte_app_path('Controllers' . $add_dir);

        $class = class_entity($name)
            ->wrap('php')
            ->extend('Controller')
            ->namespace($namespace);

        if ($this->option('resource')) {

            $class->method('index')->line()
                ->line("return view('".config('lte.paths.view', 'admin').".resource.list');")
                ->doc(function (DocumentorEntity $doc) { $doc->tagReturn('\Illuminate\Contracts\View\Factory|\Illuminate\View\View'); });

            $class->method('create')->line()
                ->line("return view('".config('lte.paths.view', 'admin').".resource.create');")
                ->doc(function (DocumentorEntity $doc) { $doc->tagReturn('\Illuminate\Contracts\View\Factory|\Illuminate\View\View'); });

            $class->method('edit')->line()
                ->line("return view('".config('lte.paths.view', 'admin').".resource.edit');")
                ->doc(function (DocumentorEntity $doc) { $doc->tagReturn('\Illuminate\Contracts\View\Factory|\Illuminate\View\View'); });

        }

        $file = $dir . '/' . $name . '.php';

        if (is_file($file) && !$this->option('force')) {

            $this->error("Controller [{$namespace}\\{$name}] exists!");
            return;
        }

        file_put_contents($file, $class->render());

        $this->info('Controller [' . $dir . '/' . $name . '.php] created!');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the class'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the controller already exists'],
            ['resource', 'r', InputOption::VALUE_NONE, 'Generate a resource controller class.'],
        ];
    }
}
