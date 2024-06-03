<?php

declare(strict_types=1);

namespace Admin;

use Admin\Core\NavGroup;
use Admin\Core\NavigatorExtensions;
use Admin\Core\NavItem;
use Admin\Interfaces\NavigateInterface;
use Admin\Traits\NavDefaultToolsTrait;
use Broadcast;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Routing\Router;

/**
 * Parent navigation class. This class is responsible for creating the navigation menu in the admin panel.
 *
 * @mixin NavigatorExtensions
 */
class NavigateEngine implements NavigateInterface
{
    use NavDefaultToolsTrait;

    /**
     * A global list of menu items from which navigation is based.
     *
     * @var array
     */
    public static array $items = [];

    /**
     * Current application router.
     *
     * @var Router
     */
    public static Router $router;

    /**
     * Extension provider.
     *
     * @var ExtendProvider|null
     */
    public static ExtendProvider|null $extension = null;

    /**
     * Execute a callback on the current navigation instance.
     *
     * @param  Closure|array  ...$calls
     * @return $this
     */
    public static function do(...$calls): static
    {
        foreach ($calls as $call) {
            call_user_func($call, \Navigate::instance(), static::$router);
        }

        return \Navigate::instance();
    }

    /**
     * Get the current navigator instance.
     *
     * @return $this
     */
    public function instance(): static
    {
        return $this;
    }

    /**
     * Add a header with title to the menu.
     *
     * @param  string  $title
     * @return $this
     */
    public function menu_header(string $title): static
    {
        self::$items[] = collect(['main_header' => $title]);

        return $this;
    }

    /**
     * Add a template that will be displayed in the navigation which is in the header.
     *
     * @param  string  $view
     * @param  array  $params
     * @param  bool  $prepend
     * @return $this
     */
    public function nav_bar_view(string $view, array $params = [], bool $prepend = false): static
    {
        self::$items[] = collect(['nav_bar_view' => $view, 'params' => $params, 'prepend' => $prepend]);

        return $this;
    }

    /**
     * Add a Vue component that will be displayed in the navigation which is in the header.
     *
     * @param  string  $class
     * @param  array  $params
     * @param  bool  $prepend
     * @return $this
     */
    public function nav_bar_vue(string $class, array $params = [], bool $prepend = false): static
    {
        self::$items[] = collect(['nav_bar_vue' => $class, 'params' => $params, 'prepend' => $prepend]);

        return $this;
    }

    /**
     * Add a template that will be displayed in the left navigation which is in the header.
     *
     * @param  string  $view
     * @param  array  $params
     * @return $this
     */
    public function left_nav_bar_view(string $view, array $params = []): static
    {
        self::$items[] = collect(['left_nav_bar_view' => $view, 'params' => $params]);

        return $this;
    }

    /**
     * Add a Vue component that will be displayed in the left navigation which is in the header.
     *
     * @param  string  $class
     * @param  array  $params
     * @return $this
     */
    public function left_nav_bar_vue(string $class, array $params = []): static
    {
        self::$items[] = collect(['left_nav_bar_vue' => $class, 'params' => $params]);

        return $this;
    }

    /**
     * Create a group of menu items.
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
     * Integrate extension navigation.
     *
     * @param $name
     * @return void
     */
    protected function includeAfterGroup($name): void
    {
        if (is_string($name) && isset(AdminEngine::$nav_extensions[$name]) && is_array(AdminEngine::$nav_extensions[$name])) {
            foreach (AdminEngine::$nav_extensions[$name] as $item) {
                if (!is_array($item)) {
                    self::$extension = $item;

                    $item->navigator($this);

                    self::$extension = null;
                }
            }

            unset(AdminEngine::$nav_extensions[$name]);
        }
    }

    /**
     * Add a menu item.
     *
     * @param  string|null  $title
     * @param  string|null  $route
     * @param  string|Closure|array|null  $action
     * @return NavItem
     */
    public function item(string $title = null, string $route = null, $action = null): NavItem
    {
        $item = new NavItem($title, $route, $action);

        self::$items[] = $item;

        if (isset($item->items['route'])) {
            $this->includeAfterGroup($item->items['route']);
        }

        return $item;
    }

    /**
     * Get all the menu items that exist.
     *
     * @return array
     */
    public function get(): array
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
     * Get all the menu items in their raw form as they are stored.
     *
     * @return array
     */
    public function getRawItems(): array
    {
        return self::$items;
    }

    /**
     * Register a channel authenticator.
     *
     * @param  string  $channel
     * @param  callable|string|array  $callback
     * @param  array  $options
     * @return $this
     */
    public function channel(string $channel, callable|string|array $callback, array $options = []): static
    {
        Broadcast::channel($channel, $callback, $options);

        return $this;
    }

    /**
     * Magic method that adds support for calling extensions by slug.
     *
     * @param $name
     * @param $arguments
     * @return NavigateEngine
     */
    public function __call($name, $arguments)
    {
        if (isset(AdminEngine::$nav_extensions[$name])) {
            self::$extension = AdminEngine::$nav_extensions[$name];

            AdminEngine::$nav_extensions[$name]->navigator($this);

            self::$extension = null;

            unset(AdminEngine::$nav_extensions[$name]);
        }

        return $this;
    }
}
