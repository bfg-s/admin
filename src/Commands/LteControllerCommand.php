<?php

namespace Lar\LteAdmin\Commands;

use Illuminate\Console\Command;
use Lar\EntityCarrier\Core\Entities\DocumentorEntity;
use Lar\LteAdmin\Segments\Info;
use Lar\LteAdmin\Segments\Matrix;
use Lar\LteAdmin\Segments\Sheet;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;
use Lar\LteAdmin\Segments\Tagable\ModelTable;
use Lar\LteAdmin\Segments\Tagable\SearchForm;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class LteUpdateAssets
 *
 * @package Lar\LteAdmin\Commands
 */
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

        $model = $this->hasOption('model') ? (
            $this->option('model') ? $this->option('model') : preg_replace('/(.*)Controller$/', '$1', $name)
        ) : false;

        $resource = $this->option('resource');

        if ($model) {

            $resource = true;
        }

        if (!preg_match('/Controller$/', $name)) {
            $name .= "Controller";
        }

        $ex = explode("/", $name);

        $add_dir = "";

        $namespace = lte_app_namespace('Controllers');

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

        if ($resource) {

            $class->use(Info::class)
                ->use(Sheet::class)
                ->use(Matrix::class)
                ->use(Form::class)
                ->use(ModelTable::class)
                ->use(ModelInfoTable::class);

            $class->prop('static:model');

            if ($model) {

                if (!class_exists("App\\{$model}")) {
                    $model_namespace = "App\\Models\\{$model}";
                } else {
                    $model_namespace = "App\\{$model}";
                }

                $class->prop("static:model", entity($model_namespace."::class"));
            }

            $class->method('index')->line()
                ->line("return Sheet::create(function (ModelTable \$table) {")
                ->line()
                ->tab("\$table->search->id();")
                ->tab("\$table->search->at();")
                ->line()
                ->tab("\$table->id();")
                ->tab("\$table->at();")
                ->line("});")
                ->doc(function (DocumentorEntity $doc) { $doc->tagReturn(Sheet::class); });

            $class->method('matrix')->line()
                ->line("return new Matrix(function (Form \$form) {")
                ->tab("\$form->info_id();")
                ->tab("\$form->autoMake();")
                ->tab("\$form->info_at();")
                ->line("});")
                ->doc(function (DocumentorEntity $doc) { $doc->tagReturn(Matrix::class); });

            $class->method('show')->line()
                ->line("return Info::create(function (ModelInfoTable \$table) {")
                ->tab("\$table->id();")
                ->tab("\$table->at();")
                ->line("});")
                ->doc(function (DocumentorEntity $doc) { $doc->tagReturn(Info::class); });

        }

        $file = $dir . '/' . $name . '.php';

        if (is_file($file) && !$this->option('force')) {

            $this->error("Controller [{$namespace}\\{$name}] exists!");
            return;
        }

        file_put_contents($file, $class->render());

        $this->info('Controller [' . $dir . '/' . $name . '.php] created!');

        if ($model && isset($model_namespace) && !class_exists($model_namespace)) {

            $this->call("make:model", [
                'name' => "Models/" . $model,
                '--migration' => true,
                '--factory' => true,
                '--seed' => true
            ]);

            $this->call("make:getter", [
                'name' => $model
            ]);

            $this->call("make:jax", [
                'name' => $model
            ]);
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
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Inject or create model from App\\Models.'],
        ];
    }
}
