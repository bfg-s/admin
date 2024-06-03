<?php

namespace Admin\Traits;

use Admin\Controllers\SystemController;

/**
 * Trait assistant that adds the ability to register and link backend callbacks with the frontend.
 */
trait ComponentPublicEventsTrait
{
    /**
     * Add an event for clicking on a component.
     *
     * @param  callable  $callback
     * @param  array  $parameters
     * @return $this
     */
    public function click(callable $callback, array $parameters = []): static
    {
        $this->on_click(
            static::registerCallBack($callback, $parameters, $this->model)
        );

        return $this;
    }

    /**
     * Add an event to dblclick on the component.
     *
     * @param  callable  $callback
     * @param  array  $parameters
     * @return $this
     */
    public function dblclick(callable $callback, array $parameters = []): static
    {
        $this->on_dblclick(
            static::registerCallBack($callback, $parameters, $this->model)
        );

        return $this;
    }

    /**
     * Add a hover event for the component.
     *
     * @param  callable  $callback
     * @param  array  $parameters
     * @return $this
     */
    public function hover(callable $callback, array $parameters = []): static
    {
        $this->on_hover(
            static::registerCallBack($callback, $parameters, $this->model)
        );

        return $this;
    }

    /**
     * Register a backend callback for a frontend event.
     *
     * @param  callable  $callback
     * @param  array  $parameters
     * @param $model
     * @return array[]
     */
    public static function registerCallBack(callable $callback, array $parameters = [], $model = null): array
    {
        SystemController::$componentEventCallbacks[] = $callback;

        if ($model) {
            foreach ($parameters as $key => $parameter) {
                if (is_int($key) && is_string($parameter)) {
                    $parameters[$parameter] = multi_dot_call($model, $parameter);
                    unset($parameters[$key]);
                } else {
                    if (is_callable($parameter)) {
                        $parameters[$key] = call_user_func($parameter, $model);
                    }
                }
            }
        }

        return [
            'admin::call_callback' => [
                array_key_last(SystemController::$componentEventCallbacks),
                $parameters
            ]
        ];
    }
}
