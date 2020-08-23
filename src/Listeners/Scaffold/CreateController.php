<?php

namespace Lar\LteAdmin\Listeners\Scaffold;

use Lar\EntityCarrier\Core\Entities\DocumentorEntity;
use Lar\LteAdmin\Events\Scaffold;
use Lar\LteAdmin\Segments\Info;
use Lar\LteAdmin\Segments\Matrix;
use Lar\LteAdmin\Segments\Sheet;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;
use Lar\LteAdmin\Segments\Tagable\ModelTable;

/**
 * Class CreateController
 * @package App\Listeners\Lar\LteAdmin\Listeners\Scaffold
 */
class CreateController
{
    /**
     * Handle the event.
     *
     * @param  Scaffold  $event
     * @return void
     */
    public function handle(Scaffold $event)
    {
        if ($event->create['controller']) {
            if (class_exists($event->controller)) {
                respond()->toast_error("Controller [{$event->controller}] already exists!");
                return ;
            }
            $f = collect($event->fields);
            $controller_parts = $event->data['controller'];
            $class_name = $controller_parts[array_key_last($controller_parts)];
            unset($controller_parts[array_key_last($controller_parts)]);
            $class = class_entity($class_name);
            $class->wrap('php');
            $class->namespace(implode("\\", $controller_parts));
            $class->use(Sheet::class);
            $class->use(Matrix::class);
            $class->use(Info::class);
            $class->use(ModelInfoTable::class);
            $class->use(ModelTable::class);
            $class->use(Form::class);
            $class->extend('Controller');
            $class->prop('static:model', entity("{$event->model}::class"));
            $method = $class->method('index');
            $method->doc(function (DocumentorEntity $doc) { $doc->tagReturn(Sheet::class); });
            $method->line("return Sheet::create(function (ModelTable \$table) {");
            foreach ($f as $field) {
                if ($field['name'] == 'id') { $method->tab("\$table->search->id();"); }
                else if ($field['field'] && $field['field'] !== 'none') {
                    if ($field['field'] != 'none') {
                        $method->tab("\$table->search->{$field['field']}('{$field['name']}', '{$field['title']}');");
                    }
                }
            }
            if ($event->created_at && $event->updated_at) {
                $method->tab("\$table->search->at();");
            } else {
                if ($event->created_at) {
                    $method->tab("\$table->search->created_at();");
                }
                if ($event->updated_at) {
                    $method->tab("\$table->search->updated_at();");
                }
            }
            foreach ($f as $field) {
                if ($field['field'] && $field['field'] != 'none') {
                    $method->tab(
                        "\$table->column('{$field['title']}', '{$field['name']}')->sort('{$field['name']}')".(
                            $field['comment'] !== null && $field['comment'] !== '' ? "->info('{$field['comment']}')" : ''
                        ).";"
                    );
                }
            }
            if ($event->created_at && $event->updated_at) {
                $method->tab("\$table->at();");
            } else {
                if ($event->created_at) {
                    $method->tab("\$table->created_at();");
                }
                if ($event->updated_at) {
                    $method->tab("\$table->updated_at();");
                }
            }
            $method->line("});");

            $method = $class->method('matrix');
            $method->doc(function (DocumentorEntity $doc) { $doc->tagReturn(Matrix::class); });
            $method->line("return Matrix::create(function (Form \$form) {");
            foreach ($f as $field) {
                if ($field['name'] == 'id') {
                    $method->tab("\$form->info_id();");
                } else if ($field['field'] && $field['field'] !== 'none') {
                    $method->tab("\$form->{$field['field']}('{$field['name']}', '{$field['title']}')".(
                            !$field['nullable'] && $field['field'] !== 'info' ? '->required()' : ''
                        ).(
                            $field['default'] !== null && $field['default'] !== '' ? (
                                $field['default'] === ' ' ? "->default('')" : "->default('{$field['default']}')"
                            ) : ""
                        ).(
                            $field['comment'] !== null && $field['comment'] !== '' ? "->info('{$field['comment']}')" : ''
                        ).";");
                }
            }
            if ($event->created_at && $event->updated_at) {
                $method->tab("\$form->info_at();");
            } else {
                if ($event->created_at) {
                    $method->tab("\$form->info('created_at', 'lte.created_at');");
                }
                if ($event->updated_at) {
                    $method->tab("\$form->info('updated_at', 'lte.updated_at');");
                }
            }
            $method->line("});");

            $method = $class->method('show');
            $method->doc(function (DocumentorEntity $doc) { $doc->tagReturn(Info::class); });

            $method->line("return Info::create(function (ModelInfoTable \$table) {");

            foreach ($f as $field) {
                if ($field['name'] == 'id') {
                    $method->tab("\$table->id();");
                } else if ($field['field'] && $field['field'] !== 'none') {
                    $method->tab("\$table->row('{$field['title']}', '{$field['name']}');");
                }
            }
            if ($event->created_at && $event->updated_at) {
                $method->tab("\$table->at();");
            } else {
                if ($event->created_at) {
                    $method->tab("\$table->created_at();");
                }
                if ($event->updated_at) {
                    $method->tab("\$table->updated_at();");
                }
            }

            $method->line("});");

            $path = base_path(str_replace('App/', 'app/', implode("/", $controller_parts)));

            if (!is_dir($path)) {

                mkdir($path, 0777, true);
            }

            if (file_put_contents($path. '/' . $class_name . '.php', $class->render())) {

                respond()->toast_success("Controller [{$event->controller}] created!");
            }
        }
    }
}
