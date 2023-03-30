<?php

namespace Admin\Components\Fields;

use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\INPUT;
use Lar\Layout\Tags\TEXTAREA;
use Admin\Components\FormGroupComponent;

class TextareaField extends FormGroupComponent
{
    /**
     * @var null
     */
    protected $icon = null;

    /**
     * @var bool
     */
    protected $form_control = true;

    /**
     * @return Component|INPUT|mixed
     */
    public function field()
    {
        return TEXTAREA::create([
            'id' => $this->field_id,
            'name' => $this->name,
            'placeholder' => $this->title,
        ], ...$this->params)
            ->text($this->value)
            ->setRules($this->rules)
            ->setDatas($this->data)
            ->addClassIf($this->has_bug, 'is-invalid')
            ->addClassIf($this->form_control, 'form-control');
    }

    /**
     * @param  int  $rows
     * @return $this
     */
    public function rows(int $rows)
    {
        $this->params[]['rows'] = $rows;

        return $this;
    }
}
