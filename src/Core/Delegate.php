<?php

namespace LteAdmin\Core;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use LteAdmin\Components\Component;
use LteAdmin\Components\LangComponent;

/**
 * @property-read LangComponent|static $lang
 */
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

    public function __get(string $name)
    {
        if ($this->condition) {

            $this->methods[] = [$name, []];
        }

        return $this;
    }

    public function __call($name, $arguments)
    {
        if ($this->condition) {
            $this->methods[] = [$name, $arguments];
        }

        return $this;
    }
}
