<?php

namespace LteAdmin\Core;

use LteAdmin\Page;

/**
 * @template DelegatedClass
 * @mixin DelegatedClass
 */
abstract class Delegator
{
    /**
     * @var DelegatedClass
     */
    protected $class;

    protected $condition = true;

    public function __call(string $name, array $arguments)
    {
        $result = (new Delegate($this->class, $this->condition))->__call($name, $arguments);
        $this->condition = true;

        return $result;
    }

    public function ifIndex()
    {
        $router = app('router');
        $this->if($router->currentRouteNamed('*.index'));

        return $this;
    }

    public function if($condition)
    {
        $this->condition = is_callable($condition) ? call_user_func($condition) : $condition;

        return $this;
    }

    public function ifCreate()
    {
        $router = app('router');
        $this->if(
            $router->currentRouteNamed('*.create')
            || $router->currentRouteNamed('*.store')
        );

        return $this;
    }

    public function ifEdit()
    {
        $router = app('router');
        $this->if(
            $router->currentRouteNamed('*.edit')
            || $router->currentRouteNamed('*.update')
        );

        return $this;
    }

    public function ifForm()
    {
        $router = app('router');
        $this->if(
            $router->currentRouteNamed('*.edit')
            || $router->currentRouteNamed('*.update')
            || $router->currentRouteNamed('*.create')
            || $router->currentRouteNamed('*.store')
        );

        return $this;
    }

    public function ifShow(...$delegates)
    {
        $router = app('router');
        $this->if($router->currentRouteNamed('*.show'));

        return $this;
    }

    public function ifQuery(string $path, mixed $need_value = true)
    {
        $val = request($path);
        if (is_array($need_value)) {
            return in_array($val, $need_value);
        }

        $this->if($need_value == (is_bool($need_value) ? (bool) $val : $val));

        return $this;
    }

    public function ifNotQuery(string $path, mixed $need_value = true)
    {
        $val = request($path);
        if (is_array($need_value)) {
            return in_array($val, $need_value);
        }

        $this->ifNot($need_value == (is_bool($need_value) ? (bool) $val : $val));

        return $this;
    }

    public function ifNot($condition)
    {
        $this->condition = !(is_callable($condition) ? call_user_func($condition) : $condition);

        return $this;
    }

    /**
     * @param  string  $path
     * @param  mixed  $need_value
     * @return bool
     */
    public function isNotModelInput(string $path, mixed $need_value = true)
    {
        return !$this->isModelInput($path, $need_value);
    }

    /**
     * @param  string  $path
     * @param  mixed  $need_value
     * @return bool
     */
    public function isModelInput(string $path, mixed $need_value = true)
    {
        $val = old($path, $this->modelInput($path));
        if (is_array($need_value)) {
            return in_array($val, $need_value);
        }

        return $need_value == (is_bool($need_value) ? (bool) $val : $val);
    }

    public function modelInput(string $path, $default = null)
    {
        $model = app(Page::class)->model();

        if ($model && $model->exists && !request()->has($path)) {
            return multi_dot_call($model, $path) ?: $default;
        }

        return request($path, $default);
    }
}
