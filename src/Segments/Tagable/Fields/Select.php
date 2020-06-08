<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Lar\LteAdmin\Segments\Tagable\Cores\CoreSelect2;
use Lar\LteAdmin\Segments\Tagable\FormGroup;

/**
 * Class Select2
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Select extends FormGroup
{
    /**
     * @var string
     */
    protected $icon = "fas fa-mouse-pointer";

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $class = "form-control";

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'select2'
    ];

    /**
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed
     */
    public function field()
    {
        return CoreSelect2::create($this->options, [
            'name' => $this->name,
            'data-placeholder' => $this->title
        ], ...$this->params)
            ->setValues($this->value)
            ->makeOptions()
            ->setDatas($this->data)
            ->addClassIf($this->has_bug, 'is-invalid')
            ->addClass($this->class);
    }

    /**
     * @param  array  $options
     * @return $this
     */
    public function options(array $options)
    {
        $this->options = $options;

        return $this;
    }
}