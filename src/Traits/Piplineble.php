<?php

namespace Admin\Traits;

trait Piplineble
{
    /**
     * @var array
     */
    public static $pipes = [];

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

        static::$pipes[$name][$type] = array_merge(static::$pipes[$name][$type], (array) $classes);
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
