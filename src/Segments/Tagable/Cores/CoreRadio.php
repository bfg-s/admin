<?php

namespace Lar\LteAdmin\Segments\Tagable\Cores;

use Illuminate\Contracts\Support\Arrayable;
use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\INPUT;
use Lar\Tagable\Events\onRender;

/**
 * Class CoreRadio.
 * @package Lar\LteAdmin\Segments\Tagable\Cores
 */
class CoreRadio extends DIV implements onRender
{
    /**
     * @var string[]
     */
    protected $props = [
        'clearfix mb-0',
        'data-inputable' => '',
    ];

    /**
     * @var array|Arrayable
     */
    protected $values;

    /**
     * @var string|array
     */
    protected $value;

    /**
     * Col constructor.
     * @param array|Arrayable $values
     * @param  mixed  ...$params
     */
    public function __construct($values, ...$params)
    {
        parent::__construct();

        $this->when($params);

        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        if (! is_array($values)) {
            $values = [$values];
        }

        $this->values = $values;
    }

    /**
     * @param $id
     * @return $this
     */
    public function id($id)
    {
        if ($id) {
            $this->id = $id;
        }

        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function name($name)
    {
        if ($name) {
            $this->name = $name;
            $this->setName($name);
        }

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function value($value)
    {
        if ($value !== null) {
            $this->value = $value;
        } else {
            $this->value = $this->getValue();
        }

        return $this;
    }

    /**
     * @return mixed|void
     */
    public function onRender()
    {
        if ($this->values) {
            $i = 0;
            foreach ($this->values as $value => $title) {
                $id = $this->id ? 'radio-'.$this->id.'-'.$i : 'radio-'.$i;

                $this->div(
                    ['icheck-primary float-left mr-3'],
                    INPUT::create(['type' => 'radio', 'id' => $id, 'name' => $this->name, 'value' => $value])
                        ->setCheckedIf($this->value == $value, 'true')
                        ->label(['for' => $id], $title)
                );

                $i++;
            }
        }
    }
}
