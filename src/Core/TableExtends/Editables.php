<?php

namespace Lar\LteAdmin\Core\TableExtends;

use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\A;
use Lar\LteAdmin\Components\FieldComponent;

class Editables
{
    /**
     * @param $value
     * @param  array  $props
     * @param  Model|null  $model
     * @param  null  $field
     * @return Component
     */
    public function input_switcher($value, array $props = [], Model $model = null, $field = null)
    {
        if ($model) {
            return FieldComponent::switcher($field)
                ->only_input()
                ->labels(...$props)
                ->switchSize('mini')
                ->value($value)->on_mouseup('jax.lte_admin.custom_save', [
                    get_class($model),
                    $model->id,
                    $field,
                    '>>$:is(:checked)',
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
                'value' => is_array($val) ? json_encode($val) : $val,
            ])->on_load('editable')->text($value);
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
    public function textarea_editable(
        $value,
        array $props = [],
        Model $model = null,
        $field = null,
        $title = null
    ) {
        return $this->editable($value, $model, $title, $field, 'textarea');
    }
}
