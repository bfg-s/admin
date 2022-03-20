<?php

namespace LteAdmin\Traits;

use Closure;

/**
 * Trait Eventable.
 *
 * @package LteAdmin\Traits
 */
trait Eventable
{
    /**
     * @var array
     */
    public static $__class_events = [];
    /**
     * @var array
     */
    private $__event_groups = [];
    /**
     * @var mixed
     */
    private $__last_data;

    /**
     * @param  Closure|array  $call
     */
    public static function onConstructed($call)
    {
        static::event(static::class, $call);
    }

    /**
     * @param $name
     * @param $call
     */
    public static function event($name, $call)
    {
        $owner = static::class;

        if (is_array($name)) {
            $name = implode('.', $name);
        }

        static::$__class_events[$owner][$name][] = $call;
    }

    /**
     * @param  Closure|array  $call
     */
    public static function onBuild($call)
    {
        static::event([static::class, 'render'], $call);
    }

    /**
     * @param  string|array|int  $name
     * @param  Closure|array  $call
     * @return $this
     */
    public function addEvent($name, $call)
    {
        if (is_array($name)) {
            $name = implode('.', $name);
        }

        $this->__event_groups[$name][] = $call;

        return $this;
    }

    /**
     * @param  array  $params
     * @return $this
     */
    protected function callConstructEvents(array $params = [])
    {
        $this->callEvent(static::class, $params);

        return $this;
    }

    /**
     * @param $name
     * @param  array  $arguments
     * @param  null  $on_last_closure
     * @return mixed
     */
    public function callEvent($name, array $arguments = [], $on_last_closure = null)
    {
        if (is_array($name)) {
            $name = implode('.', $name);
        }

        $owner = static::class;

        $events = array_merge(static::$__class_events[$owner] ?? [], $this->__event_groups);

        if (isset($events[$name])) {
            foreach ($events[$name] as $event) {
                $last = call_user_func($event, $this, $this->__last_data, $name);

                if ($last) {
                    $this->__last_data = $last;
                }

                if (is_embedded_call($on_last_closure)) {
                    call_user_func($on_last_closure, $last, $this->__last_data);
                }
            }
        }

        return $this->__last_data;
    }

    /**
     * @param  array  $params
     * @return $this
     */
    protected function callRenderEvents(array $params = [])
    {
        $this->callEvent([static::class, 'render'], $params);

        return $this;
    }
}
