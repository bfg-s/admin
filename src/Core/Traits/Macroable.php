<?php

namespace Lar\LteAdmin\Core\Traits;

/**
 * Trait Macroable
 * @package Lar\LteAdmin\Core\Traits
 */
trait Macroable
{
    use \Illuminate\Support\Traits\Macroable;

    /**
     * @param  string  $name
     * @return \ReflectionFunction|null
     * @throws \ReflectionException
     */
    public static function get_macro_reflex(string $name)
    {
        if (isset(static::$macros[$name])) {

            return new \ReflectionFunction(static::$macros[$name]);
        }

        return null;
    }

    /**
     * @return array
     */
    public static function get_macro_names()
    {
        return array_keys(static::$macros);
    }

    /**
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return parent::__call($method, $parameters);
    }

    /**
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return parent::__callStatic($method, $parameters);
    }
}