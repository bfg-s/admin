<?php

namespace LteAdmin\Core;

class Delegate
{
    public $class;

    public $methods = [];

    protected $condition;

    public function __construct(string $class, $condition = true)
    {
        $this->class = $class;
        $this->condition = $condition;
    }

    public function __call($name, $arguments)
    {
        if ($this->condition) {
            $this->methods[] = [$name, $arguments];
        }

        return $this;
    }
}
