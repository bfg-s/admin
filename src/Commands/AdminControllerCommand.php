<?php

declare(strict_types=1);

namespace Admin\Commands;

use Admin\Delegates\Card;
use Admin\Delegates\Form;
use Admin\Delegates\ModelInfoTable;
use Admin\Delegates\ModelTable;
use Admin\Delegates\SearchForm;
use Admin\Delegates\Tab;
use Admin\Page;
use Bfg\Entity\Core\Entities\DocumentorEntity;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class AdminControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'admin:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make Admin Controller';

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

        if (!str_ends_with($name, 'Controller')) {
            $name .= 'Controller';
        }

        $ex = explode('/', $name);

        $add_dir = '';

        $namespace = admin_app_namespace('Controllers');

        if (count($ex) > 1) {
            $a = str_replace($ex[count($ex) - 1], '', $name);

            $add_dir = '/'.$a;

            $namespace .= '\\'.trim(str_replace('/', '\\', $a), '\\');

            $name = $ex[count($ex) - 1];
        }

        $dir = admin_app_path('Controllers'.$add_dir);

        $class = class_entity($name)
            ->wrap('php')
            ->extend('Controller')
            ->namespace($namespace);

        if ($resource) {
            $class->use(Page::class);
            $class->use(class_exists(\App\Admin\Delegates\Card::class) ? \App\Admin\Delegates\Card::class : Card::class);
            $class->use(class_exists(\App\Admin\Delegates\Form::class) ? \App\Admin\Delegates\Form::class : Form::class);
            $class->use(class_exists(\App\Admin\Delegates\SearchForm::class) ? \App\Admin\Delegates\SearchForm::class : SearchForm::class);
            $class->use(class_exists(\App\Admin\Delegates\ModelTable::class) ? \App\Admin\Delegates\ModelTable::class : ModelTable::class);
            $class->use(class_exists(\App\Admin\Delegates\ModelInfoTable::class) ? \App\Admin\Delegates\ModelInfoTable::class : ModelInfoTable::class);
            $class->use(class_exists(\App\Admin\Delegates\Tab::class) ? \App\Admin\Delegates\Tab::class : Tab::class);

            $class->prop('static:model');
            $model_namespace = null;

            if ($model) {
                if (class_exists($model)) {
                    $model_namespace = $model;
                    $model = class_basename($model);
                } else {
                    if (class_exists("App\\{$model}")) {
                        $model_namespace = "App\\{$model}";
                    } else {
                        if (class_exists("App\\Models\\{$model}")) {
                            $model_namespace = "App\\Models\\{$model}";
                        } else {
                            $model = null;
                        }
                    }
                }

                if ($model && isset($model_namespace)) {
                    $class->prop('static:model', entity($model_namespace.'::class'));
                }
            }

            $fillables = $model_namespace ? (new ($model_namespace))->getFillable() : [];

            if (in_array('index', $only) && $model) {
                $method = $class->method('index')
                    ->param('page', null, 'Page')
                    ->param('card', null, 'Card')
                    ->param('searchForm', null, 'SearchForm')
                    ->param('modelTable', null, 'ModelTable')
                    ->line('return $page->card(')
                    ->tab('$card->search_form(')
                    ->tab('    $searchForm->id(),');

                foreach ($fillables as $item) {
                    $label = Str::title(str_replace('_', ' ', Str::snake($item)));
                    $method->tab("    \$searchForm->input('$item', '$label'),");
                }

                $method->tab('    $searchForm->at(),')
                    ->tab('),')
                    ->tab('$card->statisticBody(');

                foreach ($fillables as $item) {
                    $label = Str::title(str_replace('_', ' ', Str::snake($item)));
                    $method->tab("    \$modelTable->col('$label', '$item')->sort(),");
                }

                $method->tab('),')
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
            if (in_array('matrix', $only) && $model) {
                $method = $class->method('matrix')
                    ->param('page', null, 'Page')
                    ->param('card', null, 'Card')
                    ->param('form', null, 'Form')
                    ->param('tab', null, 'Tab')
                    ->line('return $page->card(')
                    ->tab('$card->form(')
                    ->tab('    $form->tabGeneral(');
                foreach ($fillables as $item) {
                    $label = Str::title(str_replace('_', ' ', Str::snake($item)));
                    $method->tab("      \$tab->input('$item', '$label'),");
                }

                $method->tab('    ),')
                    ->tab('),')
                    ->tab('$card->footer_form(),')
                    ->line(');')
                    ->doc(static function ($doc) {
                        /** @var DocumentorEntity $doc */
                        $doc->tagParam('Page', 'page');
                        $doc->tagParam('Card', 'card');
                        $doc->tagParam('Form', 'form');
                        $doc->tagParam('Tab', 'tab');
                        $doc->tagReturn('Page');
                    })->returnType('Page');
            }
            if (in_array('show', $only) && $model) {
                $method = $class->method('show')
                    ->param('page', null, 'Page')
                    ->param('card', null, 'Card')
                    ->param('modelInfoTable', null, 'ModelInfoTable')
                    ->line('return $page->card(')
                    ->tab('$card->model_info_table(')
                    ->tab('    $modelInfoTable->id(),');

                foreach ($fillables as $item) {
                    $label = Str::title(str_replace('_', ' ', Str::snake($item)));
                    $method->tab("    \$modelInfoTable->row('$label', '$item'),");
                }

                $method->tab('    $modelInfoTable->at(),')
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
