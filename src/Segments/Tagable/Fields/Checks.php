<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Illuminate\Contracts\Support\Arrayable;
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
     * @param  array|Arrayable  $options
     * @param  bool  $first_default
     * @return $this
     */
    public function options($options, bool $first_default = false)
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $this->options = $options;

        if ($first_default) {
            $this->default(array_key_first($this->options));
        }

        return $this;
    }

    /**
     * @return $this|Checks|FormGroup
     */
    public function _front_rule_required()
    {
        $this->rules[] = 'any-checked';

        return $this;
    }
}