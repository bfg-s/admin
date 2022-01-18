<?php

namespace Lar\LteAdmin\Listeners\Scaffold;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Lar\LteAdmin\Events\Scaffold;

/**
 * Class CreateMigration.
 * @package App\Listeners\Lar\LteAdmin\Listeners\Scaffold
 */
class CreateMigration
{
    /**
     * Handle the event.
     *
     * @param  Scaffold  $event
     * @return void
     */
    public function handle(Scaffold $event)
    {
        if ($event->create['migration'] && ! Schema::hasTable($event->table_name)) {
            $name = "create_{$event->table_name}_table";
            $file_name = now()->format('Y_m_d_His')."_{$name}.php";
            $class_name = Str::studly($name);
            if (class_exists($class_name)) {
                respond()->toast_error("Migration [{$name}] already exists!");

                return;
            }
            $class = class_entity($class_name);
            $class->wrap('php');
            $class->extend(Migration::class);
            $class->use(Blueprint::class);
            $class->use(Schema::class);
            $method = $class->method('up')
                ->line("Schema::create('{$event->table_name}', function (Blueprint \$table) {");
            foreach ($event->fields as $field) {
                if ($field['name']) {
                    $method->tab("\$table->{$field['type']}('{$field['name']}'".
                        (count($field['type_props']) ? ','.(
                            $field['type'] == 'enum' || $field['type'] == 'set' ?
                                array_entity(array_map('trim', explode(',', $field['type_props'][0])))->minimized()->render() :
                                implode(',', $field['type_props'])
                        ) : '')
                        .')'.(
                            $field['nullable'] ? '->nullable()' : (
                                $field['default'] !== null && $field['default'] !== '' ? (
                                    $field['default'] === ' ' ? "->default('')" : "->default('{$field['default']}')"
                                ) : ''
                            )
                        ).(
                            $field['key'] == 'Unique' ? '->unique()' : ''
                        ).(
                            $field['key'] == 'Index' ? '->index()' : ''
                        ).(
                            $field['comment'] !== null && $field['comment'] !== '' ? "->comment('{$field['comment']}')" : ''
                        ).';');
                }
            }
            if ($event->created_at) {
                $method->tab("\$table->timestamp('created_at')->nullable();");
            }
            if ($event->updated_at) {
                $method->tab("\$table->timestamp('updated_at')->nullable();");
            }
            if ($event->soft_delete) {
                $method->tab('$table->softDeletes();');
            }

            $method->line('});');

            $class->method('down')
                ->line("Schema::dropIfExists('{$event->table_name}');");

            if (file_put_contents(database_path("migrations/$file_name"), $class->render())) {
                respond()->toast_success("Migration [{$name}] created!");
            }
        }
    }
}
