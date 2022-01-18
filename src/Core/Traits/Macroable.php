<?php

namespace Lar\LteAdmin\Core\Traits;

use ReflectionClass;
use ReflectionMethod;

/**
 * Trait Macroable.
 * @package Lar\LteAdmin\Core\Traits
 */
trait Macroable
{
    /**
     * The registered string macros.
     *
     * @var array
     */
    protected static $macros = [];

    /**
     * Register a custom macro.
     *
     * @param  string  $name
     * @param  object|callable  $macro
     * @return void
     */
    public static function macro($name, $macro)
    {
        static::$macros[$name] = $macro;
    }

    /**
     * Mix another object into the class.
     *
     * @param  object|string  $mixin
     * @param  bool  $replace
     * @return void
     *
     * @throws \ReflectionException
     */
    public static function mixin($mixin, $replace = true)
    {
        if (is_string($mixin)) {
            $mixin = new $mixin();
        }

        $methods = (new ReflectionClass($mixin))->getMethods(
            ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
        );

        foreach ($methods as $method) {
            if ($replace || ! static::hasMacro($method->name)) {
                $method->setAccessible(true);
                static::macro($method->name, $method->invoke($mixin));
            }
        }
    }

    /**
     * Checks if macro is registered.
     *
     * @param  string  $name
     * @return bool
     */
    public static function hasMacro($name)
    {
        return isset(static::$macros[$name]);
    }

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
}
