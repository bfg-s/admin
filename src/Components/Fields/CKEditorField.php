<?php

namespace Lar\LteAdmin\Components\Fields;

use Lar\LteAdmin\Components\FormGroupComponent;

class CKEditorField extends FormGroupComponent
{
    /**
     * @var null
     */
    protected $icon = null;

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'ckeditor',
    ];

    /**
     * @param  string  $name
     * @param  string  $title
     * @param  string  $id
     * @param  null  $value
     * @param  bool  $has_bug
     * @param  null  $path
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed
     */
    public function field()
    {
        return \Lar\Layout\Tags\TEXTAREA::create([
            'id' => $this->field_id,
            'name' => $this->name,
            'placeholder' => $this->title,
        ], ...$this->params)
            ->text($this->value)
            ->setRules($this->rules)
            ->addClassIf($this->has_bug, 'is-invalid')
            ->setDatas($this->data);
    }

    /**
     * @param  string  $toolbar
     * @return $this
     */
    public function toolbar(string $toolbar)
    {
        $this->data['toolbar'] = $toolbar;

        return $this;
    }
}
