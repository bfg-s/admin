<?php

declare(strict_types=1);

namespace Admin\Core;

use Admin\NavigateEngine;
use Admin\Traits\FontAwesomeTrait;
use Admin\Traits\NavCommonTrait;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

/**
 * The part of the kernel that is responsible for the navigation item.
 */
class NavItem implements Arrayable
{
    use FontAwesomeTrait;
    use NavCommonTrait;

    /**
     * Navigation rule items.
     *
     * @var array
     */
    public $items = [];

    /**
     * NavItem constructor.
     *
     * @param  string|null  $title
     * @param  string|null  $route
     * @param  null  $action
     */
    public function __construct(string $title = null, string $route = null, $action = null)
    {
        $this->route($route)
            ->title($title)
            ->action($action)
            ->extension(NavigateEngine::$extension);
    }

    /**
     * Add an action to navigation items.
     *
     * @param  Closure|string|array|null  $action
     * @return $this
     */
    public function action(Closure|string|array|null $action): static
    {
        if ($action) {

            $this->items['action'] = $action;
        }

        return $this;
    }

    /**
     * Do not use navigation item in global search.
     *
     * @return $this
     */
    public function dontUseSearch(): static
    {
        $this->items['dontUseSearch'] = true;

        return $this;
    }

    /**
     * Set the page title.
     *
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
     * Set a navigation link item.
     *
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
     * Set the blank target for the navigation item.
     *
     * @return $this
     */
    public function targetBlank(): static
    {
        $this->items['target_blank'] = true;

        return $this;
    }

    /**
     * Set the navigation item to only the specified resource types.
     *
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
     * Set the navigation item to exclude the specified resource types.
     *
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
     * Add the router's "where" to the generating router.
     *
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
     * Generated route method.
     *
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
     * Add a route template as in the routing rules.
     *
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
     * Add a route resource as in the routing rules.
     *
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
     * Ignore routing creation.
     *
     * @return $this
     */
    public function ignored(): static
    {
        $this->items['ignored'] = true;

        return $this;
    }

    /**
     * Add parameters for forming a routing link.
     *
     * @param  array  $route_params
     * @return $this
     */
    public function params(array $route_params): static
    {
        $this->items['route_params'] = $route_params;

        return $this;
    }

    /**
     * Add a model to the routing item.
     *
     * @param $model
     * @return $this
     */
    public function model($model): static
    {
        $this->items['model'] = $model;

        return $this;
    }

    /**
     * Add a post route item with the desired action.
     *
     * @param  array|string  $action
     * @return $this
     */
    public function post(array|string $action = 'Controller@update'): static
    {
        $this->items['post'] = $action;

        return $this;
    }

    /**
     * Add a delete route item with the desired action.
     *
     * @param  array|string  $action
     * @return $this
     */
    public function delete(array|string $action = 'Controller@destroy'): static
    {
        $this->items['delete'] = $action;

        return $this;
    }

    /**
     * Get all rules from items in an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->items;
    }
}
