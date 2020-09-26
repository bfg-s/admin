<?php

namespace Lar\LteAdmin\Core\TableExtends;

use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Tags\A;
use Lar\Layout\Tags\TD;
use Lar\Layout\Tags\TH;
use Lar\LteAdmin\Segments\Tagable\Field;

/**
 * Class Editables
 * @package Lar\LteAdmin\Core\TableExtends
 */
class Editables {

    /**
     * @param $value
     * @param  array  $props
     * @param  Model|null  $model
     * @param  null  $field
     * @return \Lar\Layout\Abstracts\Component
     */
    public function input_switcher($value, array $props = [], Model $model = null, $field = null)
    {
        if ($model) {

            $now = lte_now();

            if (isset($now['link.update'])) {

                return Field::switcher($field)
                    ->only_input()
                    ->labels(...$props)
                    ->switchSize('mini')
                    ->default($value)->on_mouseup_put(
                        $now['link.update']($model->getRouteKey()),
                        json_encode(['__only_has' => true, $field => ($value ? 0 : 1)])
                    );
            }

            return Field::switcher($field)
                ->only_input()
                ->labels(...$props)
                ->switchSize('mini')
                ->default($value)->on_mouseup_jax('lte_admin.custom_save', [
                    get_class($model),
                    $model->id,
                    $field,
                    '>>$:is(:checked)'
                ]);
        }

        return $value;
    }

    /**
     * @param $value
     * @param  array  $props
     * @param  Model|null  $model
     * @param  null  $field
     * @param  null  $title
     * @return string
     */
    public function input_editable(
        $value,
        array $props = [],
        Model $model = null,
        $field = null,
        $title = null
    ) {
        return $this->editable($value, $model, $title, $field, 'text');
    }

    /**
     * @param $value
     * @param  array  $props
     * @param  Model|null  $model
     * @param  null  $field
     * @param  null  $title
     * @return string
     */
    public function textarea_editable(
        $value,
        array $props = [],
        Model $model = null,
        $field = null,
        $title = null
    ) {
        return $this->editable($value, $model, $title, $field, 'textarea');
    }

    /**
     * @param $value
     * @param  Model  $model
     * @param $title
     * @param $field
     * @param $type
     * @return string
     */
    protected function editable($value, Model $model, $title, $field, $type)
    {
        $now = lte_now();

        if ($model && $now && isset($now['link.update'])) {

            $val = multi_dot_call($model, $field);

            return A::create(['href' => '#'])->setDatas([
                'title' => is_string($title) ? $title : '',
                'pk' => $model->id,
                'type' => $type,
                'url' => $now['link.update']($model->getRouteKey()),
                'name' => $field,
                'value' => is_array($val) ? json_encode($val) : $val
            ])->on_load('editable')->text($value);
        }

        return $value;
    }
}