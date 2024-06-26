<?php

declare(strict_types=1);

namespace Admin\Core\TableExtends;

use Admin\Components\FieldComponent;
use Admin\Components\Inputs\SwitcherInput;
use Admin\Components\Small\AComponent;
use Illuminate\Database\Eloquent\Model;

class Editables
{
    /**
     * Add a switch to the input column.
     *
     * @param $value
     * @param  array  $props
     * @param  Model|null  $model
     * @param  string|null  $field
     * @return SwitcherInput|FieldComponent
     */
    public function input_switcher(
        $value,
        array $props = [],
        Model $model = null,
        string $field = null
    ): SwitcherInput|FieldComponent {
        if ($model) {
            $id = uniqid('id');

            $fieldComponent = FieldComponent::create();

            $fieldComponent->switcher($field)
                ->only_input()
                ->labels(...$props)
                ->switchSize('mini')
                ->value($value)
                ->setId($id)
                ->setDatas([
                    'change' => json_encode([
                        'custom_save' => [
                            get_class($model),
                            $model->id,
                            $field,
                            $id,
                        ]
                    ])
                ]);

            return $fieldComponent;
        }

        return $value;
    }

    /**
     * Add a selector to the input column.
     *
     * @param $value
     * @param  array  $props
     * @param  Model|null  $model
     * @param $field
     * @return mixed|string
     */
    public function input_select($value, array $props = [], Model $model = null, $field = null): mixed
    {
        if ($model) {
            $options = $props[0] ?? [];
            $format = $props[1] ?? (is_array($options) ? false : 'id:name');
            $where = $props[2] ?? null;
            $id = str_replace('.', '_', 'input_'.$field.'_'.$model->id);

            return "<div style='max-width: 200px'>".FieldComponent::select($field)->on_change('custom_save', [
                    get_class($model),
                    $model->id,
                    $field,
                    $id,
                ])
                    ->only_input()
                    ->value($value)
                    ->setId($id)
                    ->when(is_array($options), fn($q) => $q->options($options, $format))
                    ->when(is_string($options), fn($q) => $q->load($options, $format, $where))
                ."</div>";
        }

        return $value;
    }

    /**
     * Add radios input to the column.
     *
     * @param $value
     * @param  array  $props
     * @param  Model|null  $model
     * @param $field
     * @return mixed|string
     */
    public function input_radios($value, array $props = [], Model $model = null, $field = null): mixed
    {
        if ($model) {
            $options = $props[0] ?? [];
            $first_default = $props[1] ?? false;
            $id = 'input_'.$field.'_'.$model->id;

            return FieldComponent::radios($field)
                ->only_input()
                ->value($value)
                ->set_name($field.'_'.$model->id)
                ->setId($id)
                ->when(is_array($options), fn($q) => $q->options($options, !!$first_default))
                ->on_change('custom_save', [
                    get_class($model),
                    $model->id,
                    $field,
                    $id,
                ]);
        }

        return $value;
    }

    /**
     * Add to the column as an editing input.
     *
     * @param $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @param  null  $field
     * @param  null  $title
     * @return \Admin\Components\Small\AComponent|string
     */
    public function input_editable(
        $value,
        array $props = [],
        Model|array $model = null,
        $field = null,
        $title = null
    ): AComponent|string {
        return $this->editable($value, $model, $title, $field, 'text');
    }

    /**
     * Add to the textarea column for editing.
     *
     * @param $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @param  null  $field
     * @param  null  $title
     * @return \Admin\Components\Small\AComponent|string
     */
    public function textarea_editable(
        $value,
        array $props = [],
        Model|array $model = null,
        $field = null,
        $title = null
    ): AComponent|string {
        return $this->editable($value, $model, $title, $field, 'textarea');
    }

    /**
     * Add a custom edit field to the column.
     *
     * @param $value
     * @param  Model|array  $model
     * @param $title
     * @param $field
     * @param $type
     * @return \Admin\Components\Small\AComponent|string
     */
    protected function editable($value, Model|array $model, $title, $field, $type): AComponent|string
    {
        $now = admin_now();

        if ($now && $now->isResource()) {
            $val = multi_dot_call($model, $field);

            return AComponent::create()->attr(['href' => '#'])->setDatas([
                'title' => is_string($title) ? $title : '',
                'pk' => $model->id,
                'type' => $type,
                'url' => $now->getLinkUpdate($model->getRouteKey()),
                'name' => $field,
                'value' => is_array($val) ? json_encode($val) : $val,
            ])->on_load('editable')->text($value)->editable();
        }

        return $value;
    }
}
