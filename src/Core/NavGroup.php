<?php

declare(strict_types=1);

namespace Admin\Core;

use Admin\AdminEngine;
use Admin\Interfaces\NavigateInterface;
use Admin\NavigateEngine;
use Admin\Traits\FontAwesomeTrait;
use Admin\Traits\NavCommonTrait;
use Admin\Traits\NavDefaultToolsTrait;
use Closure;
use Illuminate\Contracts\Support\Arrayable;

/**
 * The part of the kernel that is responsible for the navigation group.
 *
 * @mixin NavigatorExtensions
 */
class NavGroup implements Arrayable, NavigateInterface
{
    use FontAwesomeTrait;
    use NavCommonTrait;
    use NavDefaultToolsTrait;

    /**
     * List of navigation items in the group.
     *
     * @var array
     */
    public $items = [];

    /**
     * NavGroup constructor.
     *
     * @param  string|null  $title
     * @param  string|null  $route
     */
    public function __construct(string $title = null, string $route = null)
    {
        $this->title($title)
            ->route($route)
            ->extension(NavigateEngine::$extension);
    }

    /**
     * Perform a closure on the navigation group.
     *
     * @param  Closure|array  ...$calls
     * @return $this
     */
    public function do(...$calls): static
    {
        foreach ($calls as $call) {
            if (is_embedded_call($call)) {
                call_user_func($call, $this);
            }
        }

        return $this;
    }

    /**
     * Add an item to the navigation group.
     *
     * @param  string|null  $title
     * @param  string|null  $route
     * @param  string|Closure|null  $action
     * @return NavItem
     */
    public function item(string $title = null, string $route = null, $action = null): NavItem
    {
        $item = new NavItem($title, $route, $action);

        $this->items['items'][] = $item;

        if (isset($item->items['route'])) {

            $this->includeAfterGroup($item->items['route']);
        }

        return $item;
    }

    /**
     * Add a group to a navigation group.
     *
     * @param  string|null  $title
     * @param  string|null|Closure|array  $route
     * @param  array|Closure|null  $cb
     * @return NavGroup
     */
    public function group(string $title = null, $route = null, array|Closure $cb = null): NavGroup
    {
        if (is_embedded_call($route) && !is_string($route)) {
            $cb = $route;
            $route = null;
        }

        $item = new self($title, $route);

        $this->items['items'][] = $item;

        if (isset($item->items['route'])) {

            $this->includeAfterGroup($item->items['route']);
        }

        if (is_embedded_call($cb)) {

            call_user_func($cb, $item);
        }

        return $item;
    }

    /**
     * Add vue template navbar.
     *
     * @param  string  $class
     * @param  array  $params
     * @param  bool  $prepend
     * @return $this
     */
    public function nav_bar_vue(string $class, array $params = [], bool $prepend = false): static
    {
        NavigateEngine::$items[] = collect(['nav_bar_vue' => $class, 'params' => $params, 'prepend' => $prepend]);

        return $this;
    }

    /**
     * Add a navbar template.
     *
     * @param  string  $view
     * @param  array  $params
     * @param  bool  $prepend
     * @return $this
     */
    public function nav_bar_view(string $view, array $params = [], bool $prepend = false): static
    {
        NavigateEngine::$items[] = collect(['nav_bar_view' => $view, 'params' => $params, 'prepend' => $prepend]);

        return $this;
    }

    /**
     * Add left a navbar template.
     *
     * @param  string  $view
     * @param  array  $params
     * @return $this
     */
    public function left_nav_bar_view(string $view, array $params = []): static
    {
        NavigateEngine::$items[] = collect(['left_nav_bar_view' => $view, 'params' => $params]);

        return $this;
    }

    /**
     * Add left vue template navbar.
     *
     * @param  string  $class
     * @param  array  $params
     * @return $this
     */
    public function left_nav_bar_vue(string $class, array $params = []): static
    {
        NavigateEngine::$items[] = collect(['left_nav_bar_vue' => $class, 'params' => $params]);

        return $this;
    }

    /**
     * Convert the navigation group into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        if (isset($this->items['items'])) {
            foreach ($this->items['items'] as $key => $item) {
                /** @var Arrayable $item */
                $this->items['items'][$key] = $item->toArray();
            }
        }

        return $this->items;
    }

    /**
     * Magic method for inserting slug extensions into navigation.
     *
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        if (isset(AdminEngine::$nav_extensions[$name])) {
            NavigateEngine::$extension = AdminEngine::$nav_extensions[$name];

            AdminEngine::$nav_extensions[$name]->navigator($this);

            NavigateEngine::$extension = null;

            unset(AdminEngine::$nav_extensions[$name]);
        }
    }
}
