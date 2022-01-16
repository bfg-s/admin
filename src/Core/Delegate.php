<?php

namespace Lar\LteAdmin\Core;

class Delegate
{
    public $class;

    public $methods = [];

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function __call($name, $arguments)
    {
        $this->methods[] = [$name, $arguments];

        return $this;
    }
}
