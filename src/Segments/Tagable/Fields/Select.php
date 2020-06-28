<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Illuminate\Contracts\Support\Arrayable;
use Lar\LteAdmin\Segments\Tagable\Cores\CoreSelect2;
use Lar\LteAdmin\Segments\Tagable\FormGroup;

/**
 * Class Select
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
}