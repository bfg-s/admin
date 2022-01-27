<?php

namespace Lar\LteAdmin\Commands;

use Illuminate\Console\Command;
use Lar\EntityCarrier\Core\Entities\DocumentorEntity;
use Lar\LteAdmin\Delegates\Card;
use Lar\LteAdmin\Delegates\Form;
use Lar\LteAdmin\Delegates\ModelInfoTable;
use Lar\LteAdmin\Delegates\ModelTable;
use Lar\LteAdmin\Delegates\SearchForm;
use Lar\LteAdmin\Page;
use Str;
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
            Str::singular(
                preg_replace('/(.*)Controller$/', '$1', $name)
            );

        $only = $this->option('only')
            ? explode(',', $this->option('only'))
            : ['index', 'matrix', 'show'];

        $resource = $this->option('resource');

        if ($model) {
            $resource = true;
        }

        if (!preg_match('/Controller$/', $name)) {
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
            $class->use(class_exists(\App\LteAdmin\Page::class) ? \App\LteAdmin\Page::class : Page::class);
            $class->use(class_exists(\App\LteAdmin\Delegates\Card::class) ? \App\LteAdmin\Delegates\Card::class : Card::class);
            $class->use(class_exists(\App\LteAdmin\Delegates\Form::class) ? \App\LteAdmin\Delegates\Form::class : Form::class);
            $class->use(class_exists(\App\LteAdmin\Delegates\SearchForm::class) ? \App\LteAdmin\Delegates\SearchForm::class : SearchForm::class);
            $class->use(class_exists(\App\LteAdmin\Delegates\ModelTable::class) ? \App\LteAdmin\Delegates\ModelTable::class : ModelTable::class);
            $class->use(class_exists(\App\LteAdmin\Delegates\ModelInfoTable::class) ? \App\LteAdmin\Delegates\ModelInfoTable::class : ModelInfoTable::class);

            $class->prop('static:model');

            if ($model) {
                if (!class_exists("App\\{$model}")) {
                    $model_namespace = "App\\Models\\{$model}";
                } else {
                    $model_namespace = "App\\{$model}";
                }

                $class->prop('static:model', entity($model_namespace.'::class'));
            }

            if (in_array('index', $only)) {
                $class->method('index')
                    ->param('page', null, 'Page')
                    ->param('card', null, 'Card')
                    ->param('searchForm', null, 'SearchForm')
                    ->param('modelTable', null, 'ModelTable')
                    ->line('return $page->card(')
                    ->tab('$card->search_form(')
                    ->tab('    $searchForm->id(),')
                    ->tab('    $searchForm->at(),')
                    ->tab('),')
                    ->tab('$card->model_table(')
                    ->tab('    $modelTable->id(),')
                    ->tab('    $modelTable->at(),')
                    ->tab('),')
                    ->line(');')
                    ->doc(static function ($doc) {
                        /** @var DocumentorEntity $doc */
                        $doc->tagParam('Page', 'page');
                        $doc->tagParam('Card', 'card');
                        $doc->tagParam('SearchForm', 'searchForm');
                        $doc->tagParam('ModelTable', 'modelTable');
                        $doc->tagReturn('Page');
                    })->returnType('Page');
            }
            if (in_array('matrix', $only)) {
                $class->method('matrix')
                    ->param('page', null, 'Page')
                    ->param('card', null, 'Card')
                    ->param('form', null, 'Form')
                    ->line('return $page->card(')
                    ->tab('$card->form(')
                    ->tab('    $form->ifEdit()->info_id(),')
                    ->tab('    $form->ifEdit()->info_updated_at(),')
                    ->tab('    $form->ifEdit()->info_created_at(),')
                    ->tab('),')
                    ->tab('$card->footer_form(),')
                    ->line(');')
                    ->doc(static function ($doc) {
                        /** @var DocumentorEntity $doc */
                        $doc->tagParam('Page', 'page');
                        $doc->tagParam('Card', 'card');
                        $doc->tagParam('Form', 'form');
                        $doc->tagReturn('Page');
                    })->returnType('Page');
            }
            if (in_array('show', $only)) {
                $class->method('show')
                    ->param('page', null, 'Page')
                    ->param('card', null, 'Card')
                    ->param('modelInfoTable', null, 'ModelInfoTable')
                    ->line('return $page->card(')
                    ->tab('$card->model_info_table(')
                    ->tab('    $modelInfoTable->id(),')
                    ->tab('    $modelInfoTable->at(),')
                    ->tab('),')
                    ->line(');')
                    ->doc(static function ($doc) {
                        /** @var DocumentorEntity $doc */
                        $doc->tagParam('Page', 'page');
                        $doc->tagParam('Card', 'card');
                        $doc->tagParam('ModelInfoTable', 'modelInfoTable');
                        $doc->tagReturn('Page');
                    })->returnType('Page');
            }
        }

        $file = $dir.'/'.$name.'.php';

        if (is_file($file) && !$this->option('force')) {
            $this->error("Controller [{$namespace}\\{$name}] exists!");

            return;
        }

        file_put_contents($file, $class->render());

        $this->info('Controller ['.$dir.'/'.$name.'.php] created!');

        if ($resource && isset($model_namespace) && !class_exists($model_namespace)) {
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
