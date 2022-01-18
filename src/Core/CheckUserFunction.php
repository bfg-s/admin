<?php

namespace Lar\LteAdmin\Core;

/**
 * Class CheckUserFunction.
 * @package Lar\LteAdmin\Core
 * @mixin FunctionsDoc
 */
class CheckUserFunction
{
    /**
     * @var array
     */
    private $list;

    /**
     * CheckUserFunction constructor.
     * @param  array  $list
     */
    public function __construct(array $list)
    {
        $this->list = $list;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->list[$name]) && $this->list[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function __get($name)
    {
        return $this->has($name);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->list[$name] = (bool) $value;
    }
}
