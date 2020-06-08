<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Lar\LteAdmin\Segments\Tagable\FormGroup;

/**
 * Class Input
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Input extends FormGroup
{
    /**
     * @var string
     */
    protected $type = "text";

    /**
     * @var bool
     */
    protected $form_control = true;

    /**
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed
     */
    public function field()
    {
        return \Lar\Layout\Tags\INPUT::create([
            'type' => $this->type,
            'id' => $this->field_id,
            'name' => $this->name,
            'placeholder' => $this->title
        ], ...$this->params)
            ->setValue($this->value)
            ->setRules($this->rules)
            ->setDatas($this->data)
            ->addClassIf($this->has_bug, 'is-invalid')
            ->addClassIf($this->form_control, 'form-control');
    }
}