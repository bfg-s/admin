<?php

namespace Lar\LteAdmin;

use Illuminate\Contracts\Support\Arrayable;
use Lar\LteAdmin\Core\NavGroup;
use Lar\LteAdmin\Core\NavItem;
use Lar\LteAdmin\Core\Traits\NavDefaultTools;
use Lar\LteAdmin\Interfaces\NavigateInterface;
use Lar\Roads\Roads;

/**
 * Class Navigate
 * @package Lar\LteAdmin
 * @mixin \Lar\LteAdmin\Core\NavigatorExtensions
 */
class Navigate implements NavigateInterface
{
    use NavDefaultTools;

    /**
     * @var array
     */
    protected static $items = [];

    /**
     * @var Roads
     */
    public static $roads;

    /**
     * @var ExtendProvider
     */
    public static $extension;

    /**
     * @param  \Closure  ...$closures
     * @return $this
     */
    public static function do(...$closures)
    {
        foreach ($closures as $closure) {

            $closure(\Navigate::instance(), static::$roads);
        }

        return \Navigate::instance();
    }

    /**
     * @param  string  $title
     * @return $this
     */
    public function menu_header(string $title)
    {
        Navigate::$items[] = collect(['main_header' => $title]);

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
        Navigate::$items[] = collect(['nav_bar_view' => $view, 'params' => $params, 'prepend' => $prepend]);

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
     * @param  string|null  $title
     * @param  string|null|\Closure  $route
     * @param  \Closure|null  $cb
     * @return \Lar\LteAdmin\Core\NavGroup
     */
    public function group(string $title = null, $route = null, \Closure $cb = null)
    {
        if ($route instanceof \Closure) {
            $cb = $route;
            $route = null;
        }

        $item = new NavGroup($title, $route);

        Navigate::$items[] = $item;

        if (isset($item->items['route'])) {

            $this->includeAfterGroup($item->items['route']);
        }

        if ($cb) {
            $cb($item, static::$roads);
        }

        return $item;
    }

    /**
     * @param  string|null  $title
     * @param  string|null  $route
     * @param  string|\Closure|null  $action
     * @return \Lar\LteAdmin\Core\NavItem
     */
    public function item(string $title = null, string $route = null, $action = null)
    {
        $item = new NavItem($title, $route, $action);

        Navigate::$items[] = $item;

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
        foreach (Navigate::$items as $key => $item) {
            /** @var Arrayable $item */
            if (!is_array($item)) {
                Navigate::$items[$key] = $item->toArray();
            } else {
                Navigate::$items[$key] = $item;
            }
        }

        return Navigate::$items;
    }

    /**
     * @return array
     */
    public function getMaked()
    {
        return Navigate::$items;
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
        \Broadcast::channel($channel, $callback, $options);

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

            Navigate::$extension = LteAdmin::$nav_extensions[$name];

            LteAdmin::$nav_extensions[$name]->navigator($this);

            Navigate::$extension = null;

            unset(LteAdmin::$nav_extensions[$name]);
        }
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

                    Navigate::$extension = $item;

                    $item->navigator($this);

                    Navigate::$extension = null;
                }
            }


            unset(LteAdmin::$nav_extensions[$name]);
        }
    }
}