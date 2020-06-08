<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Lar\LteAdmin\Segments\Tagable\FormGroup;

/**
 * Class Input
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Textarea extends FormGroup
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
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed
     */
    public function field()
    {
        return \Lar\Layout\Tags\TEXTAREA::create([
            'id' => $this->field_id,
            'name' => $this->name,
            'placeholder' => $this->title
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