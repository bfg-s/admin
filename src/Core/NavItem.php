<?php

namespace LteAdmin\Core;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use LteAdmin\Navigate;
use LteAdmin\Traits\FontAwesome;
use LteAdmin\Traits\NavCommon;
use Str;

class NavItem implements Arrayable
{
    use FontAwesome;
    use NavCommon;

    /**
     * @var array
     */
    public $items = [];

    /**
     * NavItem constructor.
     * @param  string|null  $title
     * @param  string  $route
     * @param  string|Closure|array|null  $action
     */
    public function __construct(string $title = null, string $route = null, $action = null)
    {
        $this->route($route)
            ->title($title)
            ->action($action)
            ->extension(Navigate::$extension);
    }

    /**
     * @param  string|Closure|array  $action
     * @return $this
     */
    public function action($action)
    {
        if ($action !== null) {
            $this->items['action'] = $action;
        }

        return $this;
    }

    /**
     * @param  string|null  $title
     * @return $this
     */
    public function head_title(string $title = null)
    {
        if ($title !== null) {
            $this->items['head_title'] = $title;
        }

        return $this;
    }

    /**
     * Route methods.
     */

    /**
     * @param  string|null  $link
     * @return $this
     */
    public function link(string $link = null)
    {
        if ($link !== null) {
            $this->items['link'] = $link;
        }

        return $this;
    }

    /**
     * @param ...$methods
     * @return $this
     */
    public function only(...$methods)
    {
        if ($methods && isset($this->items['resource'])) {
            $this->items['resource_only'] = $methods;
        }

        return $this;
    }

    /**
     * @param ...$methods
     * @return $this
     */
    public function except(...$methods)
    {
        if ($methods && isset($this->items['resource'])) {
            $this->items['resource_except'] = $methods;
        }

        return $this;
    }

    /**
     * @param  string|null  $where
     * @return $this
     */
    public function where(string $where = null)
    {
        if ($where !== null) {
            $this->items['where'] = $where;
        }

        return $this;
    }

    /**
     * @param  string|null  $method
     * @return $this
     */
    public function method(string $method = null)
    {
        if ($method !== null) {
            $this->items['method'] = strtolower($method);
        }

        return $this;
    }

    /**
     * @param  string|null  $view
     * @return $this
     */
    public function view(string $view = null)
    {
        if ($view !== null) {
            $this->items['view'] = $view;
        }

        return $this;
    }

    /**
     * @param  string  $name
     * @param  string  $resource
     * @param  array  $options
     * @return $this
     */
    public function resource(string $name, string $resource, array $options = [])
    {
        $this->items['resource'] = ['name' => $name, 'action' => '\\'.$resource, 'options' => $options];

        if (!isset($this->items['route']) || !$this->items['route']) {
            $this->items['route'] = $name;
        }

        if (!isset($this->items['resource_route'])) {
            $this->items['resource_route'] = Str::singular(
                Str::contains($name, '/') ? last(explode('/', $name)) : $name
            );
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function ignored()
    {
        $this->items['ignored'] = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function has_rout()
    {
        $this->items['ignored'] = true;

        return $this;
    }

    /**
     * @param  array  $route_params
     * @return $this
     */
    public function params(array $route_params)
    {
        $this->items['route_params'] = $route_params;

        return $this;
    }

    /**
     * @param  callable  $callable
     * @return $this
     */
    public function link_params(callable $callable)
    {
        $this->items['link_params'] = $callable;

        return $this;
    }

    /**
     * @param $model
     * @return $this
     */
    public function model($model)
    {
        $this->items['model'] = $model;

        return $this;
    }

    /**
     * @param  string|array  $action
     * @return $this
     */
    public function post($action = 'Controller@update')
    {
        $this->items['post'] = $action;

        return $this;
    }

    /**
     * @param  string|array  $action
     * @return $this
     */
    public function delete($action = 'Controller@destroy')
    {
        $this->items['delete'] = $action;

        return $this;
    }

    /**
     * @param  array  $params
     * @return $this
     */
    public function badge_params(array $params)
    {
        if ($this->items['badge']) {
            $this->items['badge']['params'] = $params;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->items;
    }
}
