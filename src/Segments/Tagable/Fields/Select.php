<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Lar\Layout\Abstracts\Component;
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
        return \Lar\LteAdmin\Components\Select2::create($this->options, [
            'name' => $name,
            'data-placeholder' => $title
        ], ...$this->params)->when(function (\Lar\LteAdmin\Components\Select2 $input) use ($value) {
            $input->setValues($value ?? $this->value);
        })->makeOptions()
            ->setDatas($this->data)
            ->addClassIf($has_bug, 'is-invalid')
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