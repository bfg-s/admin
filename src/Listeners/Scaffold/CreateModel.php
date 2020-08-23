<?php

namespace Lar\LteAdmin\Listeners\Scaffold;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lar\LteAdmin\Events\Scaffold;

/**
 * Class CreateModel
 * @package App\Listeners\Lar\LteAdmin\Listeners\Scaffold\
 */
class CreateModel
{
    /**
     * Handle the event.
     *
     * @param  Scaffold  $event
     * @return void
     */
    public function handle(Scaffold $event)
    {
        if ($event->create['model']) {
            if (class_exists($event->model)) {
                respond()->toast_error("Model [{$event->model}] already exists!");
                return ;
            }
            $model_parts = $event->data['model'];
            $class_name = $model_parts[array_key_last($model_parts)];
            unset($model_parts[array_key_last($model_parts)]);
            $class = class_entity($class_name);
            $class->wrap('php');
            $class->namespace(implode("\\", $model_parts));
            $class->extend(Model::class);
            if ($event->soft_delete) {
                $class->addTrait(SoftDeletes::class);
            }
            $class->prop("public:table", $event->table_name);
            if (!$event->created_at && !$event->updated_at) {
                $class->prop('timestamps', 'false');
            } else {
                if (!$event->created_at) {
                    $class->const('CREATED_AT', 'null');
                }
                if (!$event->updated_at) {
                    $class->const('UPDATED_AT', 'null');
                }
            }
            if ($event->primary != 'id') {
                $class->prop('protected:primaryKey', $event->primary);
            }
            $fillable = [];
            $casts = [];
            foreach ($event->fields as $field) {
                if ($field['name']) {
                    $fillable[] = $field['name'];
                    if ($field['cast'] == 'decimal') {
                        $casts[$field['name']] = $field['cast'] . (isset($field['type_props'][1]) ? ":{$field['type_props'][1]}":'');
                    } else {
                        $casts[$field['name']] = $field['cast'];
                    }
                }
            }
            $class->prop('protected:fillable', $fillable);
            $class->prop('protected:casts', $casts);

            $path = base_path(str_replace('App/', 'app/', implode("/", $model_parts)));

            if (!is_dir($path)) {

                mkdir($path, 0777, true);
            }

            if (file_put_contents($path . '/' . $class_name . '.php', $class->render())) {

                respond()->toast_success("Model [{$event->model}] created!");
            }
        }
    }
}
