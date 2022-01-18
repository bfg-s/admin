<?php

namespace Lar\LteAdmin\Segments\Tagable\Cores;

use Lar\Layout\Tags\SELECT;

/**
 * Class CoreSelect2.
 * @package Lar\LteAdmin\Segments\Tagable\Cores
 */
class CoreSelect2 extends SELECT
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
        if (! $this->hasAttribute('value')) {
            $this->value = $value;
        } else {
            $this->value = $this->getValue();
            $this->removeAttribute('value');
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
