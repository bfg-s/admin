<?php

namespace Admin;

use Broadcast;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Routing\Router;
use Admin\Core\NavGroup;
use Admin\Core\NavigatorExtensions;
use Admin\Core\NavItem;
use Admin\Interfaces\NavigateInterface;
use Admin\Traits\NavDefaultTools;

/**
 * @mixin NavigatorExtensions
 */
class Navigate implements NavigateInterface
{
    use NavDefaultTools;

    /**
     * @var array
     */
    public static $items = [];

    /**
     * @var Router
     */
    public static Router $router;

    /**
     * @var ExtendProvider
     */
    public static $extension;

    /**
     * @param  Closure|array  ...$calls
     * @return $this
     */
    public static function do(...$calls)
    {
        foreach ($calls as $call) {
            call_user_func($call, \Navigate::instance(), static::$router);
        }

        return \Navigate::instance();
    }

    /**
     * @param  string  $title
     * @return $this
     */
    public function menu_header(string $title)
    {
        self::$items[] = collect(['main_header' => $title]);

        return $this;
    }

    /**
     * @param  string  $view
     * @param  array  $params
     * @param  bool  $prepend
     * @return $this
     */
    public function nav_bar_view(string $view, array $params = [], bool $prepend = false)
    {
        self::$items[] = collect(['nav_bar_view' => $view, 'params' => $params, 'prepend' => $prepend]);

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
        self::$items[] = collect(['nav_bar_vue' => $class, 'params' => $params, 'prepend' => $prepend]);

        return $this;
    }

    /**
     * @param  string  $view
     * @param  array  $params
     * @return $this
     */
    public function left_nav_bar_view(string $view, array $params = [])
    {
        self::$items[] = collect(['left_nav_bar_view' => $view, 'params' => $params]);

        return $this;
    }

    /**
     * @param  string  $class
     * @param  array  $params
     * @return $this
     */
    public function left_nav_bar_vue(string $class, array $params = [])
    {
        self::$items[] = collect(['left_nav_bar_vue' => $class, 'params' => $params]);

        return $this;
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

        $item = new NavGroup($title, $route);

        self::$items[] = $item;

        if (isset($item->items['route'])) {
            $this->includeAfterGroup($item->items['route']);
        }

        if (is_embedded_call($cb)) {
            call_user_func($cb, $item, static::$router);
        }

        return $item;
    }

    /**
     * @param $name
     * @param $to
     */
    protected function includeAfterGroup($name)
    {
        if (is_string($name) && isset(Admin::$nav_extensions[$name]) && is_array(Admin::$nav_extensions[$name])) {
            foreach (Admin::$nav_extensions[$name] as $item) {
                if (!is_array($item)) {
                    self::$extension = $item;

                    $item->navigator($this);

                    self::$extension = null;
                }
            }

            unset(Admin::$nav_extensions[$name]);
        }
    }

    /**
     * @param  string|null  $title
     * @param  string|null  $route
     * @param  string|Closure|array|null  $action
     * @return NavItem
     */
    public function item(string $title = null, string $route = null, $action = null)
    {
        $item = new NavItem($title, $route, $action);

        self::$items[] = $item;

        if (isset($item->items['route'])) {
            $this->includeAfterGroup($item->items['route']);
        }

        return $item;
    }

    /**
     * @return array
     */
    public function get()
    {
        foreach (self::$items as $key => $item) {
            /** @var Arrayable $item */
            if (!is_array($item)) {
                self::$items[$key] = $item->toArray();
            } else {
                self::$items[$key] = $item;
            }
        }

        return self::$items;
    }

    /**
     * @return array
     */
    public function getMaked()
    {
        return self::$items;
    }

    /**
     * Register a channel authenticator.
     *
     * @param  string  $channel
     * @param  callable|string  $callback
     * @param  array  $options
     * @return $this
     */
    public function channel($channel, $callback, $options = [])
    {
        Broadcast::channel($channel, $callback, $options);

        return $this;
    }

    /**
     * @return $this
     */
    public function instance()
    {
        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return Navigate
     */
    public function __call($name, $arguments)
    {
        if (isset(Admin::$nav_extensions[$name])) {
            self::$extension = Admin::$nav_extensions[$name];

            Admin::$nav_extensions[$name]->navigator($this);

            self::$extension = null;

            unset(Admin::$nav_extensions[$name]);
        }

        return $this;
    }
}
