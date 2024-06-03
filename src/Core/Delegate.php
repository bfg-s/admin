<?php

declare(strict_types=1);

namespace Admin\Core;

use Admin\Components\LangComponent;

/**
 * The part of the kernel that is responsible for delegating calls to page components.
 *
 * @property-read LangComponent|static $lang
 */
class Delegate
{
    /**
     * Class of the delegate.
     *
     * @var string
     */
    public string $class;

    /**
     * Methods of the delegate.
     *
     * @var array
     */
    public array $methods = [];

    /**
     * The condition under which the delegate will be executed.
     *
     * @var mixed|true
     */
    protected mixed $condition;

    /**
     * Delegate constructor.
     *
     * @param  string  $class
     * @param mixed $condition
     */
    public function __construct(string $class, mixed $condition = true)
    {
        $this->class = $class;
        $this->condition = $condition;
    }

    /**
     * The magic method for adding methods to the delegate.
     *
     * @param  string  $name
     * @return $this
     */
    public function __get(string $name)
    {
        if ($this->condition) {
            $this->methods[] = [$name, []];
        }

        return $this;
    }

    /**
     * The magic method for adding methods to the delegate.
     *
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        if ($this->condition) {
            $this->methods[] = [$name, $arguments];
        }

        return $this;
    }
}
