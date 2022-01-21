<?php

namespace Lar\LteAdmin\Commands;

use Illuminate\Console\Command;
use Lar\EntityCarrier\Core\Entities\DocumentorEntity;
use Lar\LteAdmin\Page;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class LteControllerCommand extends Command
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

        $model = $this->option('model') ?
            $this->option('model') :
            \Str::singular(
                preg_replace('/(.*)Controller$/', '$1', $name)
            );

        $only = $this->option('only')
            ? explode(',', $this->option('only'))
            : ['index', 'matrix', 'show'];

        $resource = $this->option('resource');

        if ($model) {
            $resource = true;
        }

        if (! preg_match('/Controller$/', $name)) {
            $name .= 'Controller';
        }

        $ex = explode('/', $name);

        $add_dir = '';

        $namespace = lte_app_namespace('Controllers');

        if (count($ex) > 1) {
            $a = str_replace($ex[count($ex) - 1], '', $name);

            $add_dir = '/'.$a;

            $namespace .= '\\'.trim(str_replace('/', '\\', $a), '\\');

            $name = $ex[count($ex) - 1];
        }

        $dir = lte_app_path('Controllers'.$add_dir);

        $class = class_entity($name)
            ->wrap('php')
            ->extend('Controller')
            ->namespace($namespace);

        if ($resource) {
            $class->use(Page::class);

            $class->prop('static:model');

            if ($model) {
                if (! class_exists("App\\{$model}")) {
                    $model_namespace = "App\\Models\\{$model}";
                } else {
                    $model_namespace = "App\\{$model}";
                }

                $class->prop('static:model', entity($model_namespace.'::class'));
            }

            if (in_array('index', $only)) {
                $class->method('index')
                    ->param('page', null, 'Page')
                    ->line('return $page')
                    ->tab('->card()')
                    ->tab('->search_form(')
                    ->tab('    $this->search_form->id(),')
                    ->tab('    $this->search_form->at(),')
                    ->tab(')')
                    ->tab('->model_table(')
                    ->tab('    $this->model_table->id(),')
                    ->tab('    $this->model_table->at(),')
                    ->tab(');')
                    ->doc(static function ($doc) {
                        /** @var DocumentorEntity $doc */
                        $doc->tagParam('Page', 'page')->tagReturn(Page::class);
                    });
            }
            if (in_array('matrix', $only)) {
                $class->method('matrix')
                    ->param('page', null, 'Page')
                    ->line('return $page')
                    ->tab('->card()')
                    ->tab('->form(')
                    ->tab('    $this->form->info_id(),')
                    ->tab('    $this->form->info_at(),')
                    ->tab(');')
                    ->doc(static function ($doc) {
                        /** @var DocumentorEntity $doc */
                        $doc->tagParam('Page', 'page')->tagReturn(Page::class);
                    });
            }
            if (in_array('show', $only)) {
                $class->method('show')
                    ->param('page', null, 'Page')
                    ->line('return $page')
                    ->tab('->card()')
                    ->tab('->model_info_table(')
                    ->tab('    $this->model_info_table->id(),')
                    ->tab('    $this->model_info_table->at(),')
                    ->tab(');')
                    ->doc(static function ($doc) {
                        /** @var DocumentorEntity $doc */
                        $doc->tagParam('Page', 'page')->tagReturn(Page::class);
                    });
            }
        }

        $file = $dir.'/'.$name.'.php';

        if (is_file($file) && ! $this->option('force')) {
            $this->error("Controller [{$namespace}\\{$name}] exists!");

            return;
        }

        file_put_contents($file, $class->render());

        $this->info('Controller ['.$dir.'/'.$name.'.php] created!');

        if ($resource && isset($model_namespace) && ! class_exists($model_namespace)) {
            $this->warn("Model [$model] not found!");
            if ($this->confirm("Create a new model [$model]?")) {
                $this->call('make:model', [
                    'name' => $model,
                ]);
            }
        }
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
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the controller already exists.'],
            ['resource', 'r', InputOption::VALUE_NONE, 'Generate a resource controller class.'],
            ['only', 'o', InputOption::VALUE_OPTIONAL, 'Select methods for generate.'],
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Inject or create model from App\\Models.'],
        ];
    }
}
