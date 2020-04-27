<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\SELECT;

/**
 * Class Select2
 * @package Lar\LteAdmin\Components
 */
class Select2 extends SELECT
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var mixed|null
     */
    private $value;

    /**
     * Col constructor.
     * @param  array  $options
     * @param  mixed  $value
     * @param  mixed  ...$params
     */
    public function __construct($options = [], ...$params)
    {
        parent::__construct();

        $this->when($params);

        $this->options = $options;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValues($value)
    {
        if (!$this->hasAttribute('value')) {

            $this->value = $value;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function makeOptions()
    {
        $this->options($this->options, $this->value ?? $this->getValue());

        return $this;
    }
}