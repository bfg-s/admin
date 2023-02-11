<?php

namespace LteAdmin\Core\TableExtends;

use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\A;
use LteAdmin\Components\FieldComponent;

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
     * @param $field
     * @return mixed|string
     */
    public function input_select($value, array $props = [], Model $model = null, $field = null)
    {
        if ($model) {

            $options = $props[0] ?? [];
            $format = $props[1] ?? (is_array($options) ? false : 'id:name');
            $where = $props[2] ?? null;

            return "<div style='max-width: 200px'>" . FieldComponent::select($field)
                ->only_input()
                ->value($value)
                ->force_set_id('input_'.$field.'_'.$model->id)
                ->when(is_array($options), fn ($q) => $q->options($options, $format))
                ->when(is_string($options), fn ($q) => $q->load($options, $format, $where))
                ->on_change('jax.lte_admin.custom_save', [
                    get_class($model),
                    $model->id,
                    $field,
                    '>>$:val()',
                ]) . "</div>";
        }

        return $value;
    }

    /**
     * @param $value
     * @param  array  $props
     * @param  Model|null  $model
     * @param $field
     * @return mixed|string
     */
    public function input_radios($value, array $props = [], Model $model = null, $field = null)
    {
        if ($model) {

            $options = $props[0] ?? [];
            $first_default = $props[1] ?? false;

            return FieldComponent::radios($field)
                ->only_input()
                ->value($value)
                ->set_name($field.'_'.$model->id)
                ->force_set_id('input_'.$field.'_'.$model->id)
                ->when(is_array($options), fn ($q) => $q->options($options, !! $first_default))
                ->on_change('jax.lte_admin.custom_save', [
                    get_class($model),
                    $model->id,
                    $field,
                    '>>lte::get_selected_radio()',
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

        if ($now && $now->isResource()) {
            $val = multi_dot_call($model, $field);

            return A::create(['href' => '#'])->setDatas([
                'title' => is_string($title) ? $title : '',
                'pk' => $model->id,
                'type' => $type,
                'url' => $now->getLinkUpdate($model->getRouteKey()),
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
