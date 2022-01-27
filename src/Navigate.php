<?php

namespace Lar\LteAdmin;

use Broadcast;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Lar\LteAdmin\Core\NavGroup;
use Lar\LteAdmin\Core\NavigatorExtensions;
use Lar\LteAdmin\Core\NavItem;
use Lar\LteAdmin\Core\Traits\NavDefaultTools;
use Lar\LteAdmin\Interfaces\NavigateInterface;
use Lar\Roads\Roads;

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
     * @var Roads
     */
    public static $roads;

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
            call_user_func($call, \Navigate::instance(), static::$roads);
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
            call_user_func($cb, $item, static::$roads);
        }

        return $item;
    }

    /**
     * @param $name
     * @param $to
     */
    protected function includeAfterGroup($name)
    {
        if (is_string($name) && isset(LteAdmin::$nav_extensions[$name]) && is_array(LteAdmin::$nav_extensions[$name])) {
            foreach (LteAdmin::$nav_extensions[$name] as $item) {
                if (!is_array($item)) {
                    self::$extension = $item;

                    $item->navigator($this);

                    self::$extension = null;
                }
            }

            unset(LteAdmin::$nav_extensions[$name]);
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
     */
    public function __call($name, $arguments)
    {
        if (isset(LteAdmin::$nav_extensions[$name])) {
            self::$extension = LteAdmin::$nav_extensions[$name];

            LteAdmin::$nav_extensions[$name]->navigator($this);

            self::$extension = null;

            unset(LteAdmin::$nav_extensions[$name]);
        }
    }
}
