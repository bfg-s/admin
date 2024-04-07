<?php

declare(strict_types=1);

namespace Admin\Core;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Admin\Navigate;
use Admin\Traits\FontAwesome;
use Admin\Traits\NavCommon;
use Illuminate\Support\Str;

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
     * @param  string|null  $route
     * @param  null  $action
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
    public function action($action): static
    {
        if ($action !== null) {
            $this->items['action'] = $action;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function dontUseSearch(): static
    {
        $this->items['dontUseSearch'] = true;

        return $this;
    }

    /**
     * @param  string|null  $title
     * @return $this
     */
    public function head_title(string $title = null): static
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
    public function link(string $link = null): static
    {
        if ($link !== null) {
            $this->items['link'] = $link;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function targetBlank(): static
    {
        $this->items['target_blank'] = true;

        return $this;
    }

    /**
     * @param ...$methods
     * @return $this
     */
    public function only(...$methods): static
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
    public function except(...$methods): static
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
    public function where(string $where = null): static
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
    public function method(string $method = null): static
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
    public function view(string $view = null): static
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
    public function resource(string $name, string $resource, array $options = []): static
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
    public function ignored(): static
    {
        $this->items['ignored'] = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function has_rout(): static
    {
        $this->items['ignored'] = true;

        return $this;
    }

    /**
     * @param  array  $route_params
     * @return $this
     */
    public function params(array $route_params): static
    {
        $this->items['route_params'] = $route_params;

        return $this;
    }

    /**
     * @param  callable  $callable
     * @return $this
     */
    public function link_params(callable $callable): static
    {
        $this->items['link_params'] = $callable;

        return $this;
    }

    /**
     * @param $model
     * @return $this
     */
    public function model($model): static
    {
        $this->items['model'] = $model;

        return $this;
    }

    /**
     * @param  array|string  $action
     * @return $this
     */
    public function post(array|string $action = 'Controller@update'): static
    {
        $this->items['post'] = $action;

        return $this;
    }

    /**
     * @param  array|string  $action
     * @return $this
     */
    public function delete(array|string $action = 'Controller@destroy'): static
    {
        $this->items['delete'] = $action;

        return $this;
    }

    /**
     * @param  array  $params
     * @return $this
     */
    public function badge_params(array $params): static
    {
        if ($this->items['badge']) {
            $this->items['badge']['params'] = $params;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->items;
    }
}
