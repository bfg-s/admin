<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Lar\LteAdmin\Segments\Tagable\Cores\CoreCheckBox;
use Lar\LteAdmin\Segments\Tagable\FormGroup;

/**
 * Class Input
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Checks extends FormGroup
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var null
     */
    protected $icon = null;

    /**
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed
     */
    public function field()
    {
        return CoreCheckBox::create($this->options)
            ->name($this->name)
            ->id($this->field_id)
            ->value($this->value)
            ->setRules($this->rules)
            ->setDatas($this->data);
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

    /**
     * @return $this|Checks|FormGroup
     */
    public function isRequired()
    {
        $this->rules[] = 'any-checked';

        return $this;
    }
}