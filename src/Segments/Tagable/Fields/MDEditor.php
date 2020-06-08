<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Lar\LteAdmin\Segments\Tagable\FormGroup;

/**
 * Class Input
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class MDEditor extends FormGroup
{
    /**
     * @var null
     */
    protected $icon = null;

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'md::simple'
    ];

    /**
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed
     */
    public function field()
    {
        return \Lar\Layout\Tags\DIV::create([
            'id' => $this->field_id,
            'data-name' => $this->name,
            'data-placeholder' => $this->title,
            'm-0'
        ], ...$this->params)
            ->text($this->value)
            ->setRules($this->rules)
            ->setDatas($this->data)
            ->addClassIf($this->has_bug, 'is-invalid');
    }
}