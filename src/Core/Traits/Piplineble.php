<?php

namespace Lar\LteAdmin\Core\Traits;

/**
 * Trait Piplineble
 * @package Lar\LteAdmin\Core\Traits
 */
trait Piplineble
{
    /**
     * @var array
     */
    static $pipes = [];

    /**
     * @param  array|string  $classes
     * @param  string|null  $type
     */
    public static function pipes($classes, string $type = 'default')
    {
        $name = static::class;

        if (!isset(static::$pipes[$name][$type])) {

            static::$pipes[$name][$type] = [];
        }

        static::$pipes[$name][$type] = array_merge(static::$pipes[$name][$type], (array)$classes);
    }

    /**
     * @param $subject
     * @param  string  $type
     * @return mixed
     */
    public static function fire_pipes($subject, string $type = 'default')
    {
        $name = static::class;

        if (!isset(static::$pipes[$name][$type])) {

            return $subject;
        }

        return pipeline($subject, static::$pipes[$name][$type]);
    }
}