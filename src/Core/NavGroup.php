<?php

namespace Admin\Core;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Admin\Interfaces\NavigateInterface;
use Admin\Admin;
use Admin\Navigate;
use Admin\Traits\FontAwesome;
use Admin\Traits\NavCommon;
use Admin\Traits\NavDefaultTools;

/**
 * @mixin NavigatorExtensions
 */
class NavGroup implements Arrayable, NavigateInterface
{
    use FontAwesome;
    use NavCommon;
    use NavDefaultTools;

    /**
     * @var array
     */
    public $items = [];

    /**
     * NavItem constructor.
     * @param  string|null  $title
     * @param  string  $route
     */
    public function __construct(string $title = null, string $route = null)
    {
        $this->title($title)
            ->route($route)
            ->extension(Navigate::$extension);
    }

    /**
     * @param  Closure|array  ...$calls
     * @return $this
     */
    public function do(...$calls)
    {
        foreach ($calls as $call) {
            if (is_embedded_call($call)) {
                call_user_func($call, $this);
            }
        }

        return $this;
    }

    /**
     * @param  string|null  $title
     * @param  string|null  $route
     * @param  string|Closure|null  $action
     * @return NavItem
     */
    public function item(string $title = null, string $route = null, $action = null)
    {
        $item = new NavItem($title, $route, $action);

        $this->items['items'][] = $item;

        if (isset($item->items['route'])) {
            $this->includeAfterGroup($item->items['route']);
        }

        return $item;
    }

    /**
     * @param  string|null  $title
     * @param  string|null|Closure|array  $route
     * @param  Closure|array|null  $cb
     * @return NavGroup
     */
    public function group(string $title = null, $route = null, $cb = null)
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
     * @param  string  $view
     * @param  array  $params
     * @param  bool  $prepend
     * @return $this
     */
    public function nav_bar_view(string $view, array $params = [], bool $prepend = false)
    {
        Navigate::$items[] = collect(['nav_bar_view' => $view, 'params' => $params, 'prepend' => $prepend]);

        return $this;
    }

    /**
     * @param  string  $class
     * @param  array  $params
     * @param  bool  $prepend
     * @return $this
     */
    public function nav_bar_vue(string $class, array $params = [], bool $prepend = false)
    {
        Navigate::$items[] = collect(['nav_bar_vue' => $class, 'params' => $params, 'prepend' => $prepend]);

        return $this;
    }

    /**
     * @param  string  $view
     * @param  array  $params
     * @return $this
     */
    public function left_nav_bar_view(string $view, array $params = [])
    {
        Navigate::$items[] = collect(['left_nav_bar_view' => $view, 'params' => $params]);

        return $this;
    }

    /**
     * @param  string  $class
     * @param  array  $params
     * @return $this
     */
    public function left_nav_bar_vue(string $class, array $params = [])
    {
        Navigate::$items[] = collect(['left_nav_bar_vue' => $class, 'params' => $params]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
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
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        if (isset(Admin::$nav_extensions[$name])) {
            Navigate::$extension = Admin::$nav_extensions[$name];

            Admin::$nav_extensions[$name]->navigator($this);

            Navigate::$extension = null;

            unset(Admin::$nav_extensions[$name]);
        }
    }
}
