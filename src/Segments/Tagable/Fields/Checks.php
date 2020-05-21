<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Lar\LteAdmin\Components\HorizontalCheckBox;
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
     * @param  string  $name
     * @param  string  $title
     * @param  string  $id
     * @param  null  $value
     * @param  bool  $has_bug
     * @param  null  $path
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed
     */
    public function field(string $name, string $title, string $id = '', $value = null, bool $has_bug = false, $path = null)
    {
        return HorizontalCheckBox::create($this->options)->name($name)->id($id)->value($value)->setRules($this->rules)
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