<?php

declare(strict_types=1);

namespace Admin\Core;

use Admin\ExtendProvider;
use Admin\Interfaces\NavigateInterface;
use Admin\NavigateEngine;
use Closure;

/**
 * The part of the kernel that is responsible for navigating the extension.
 *
 * @mixin NavigatorMethods
 */
class NavigatorExtensionProvider implements NavigateInterface
{
    /**
     * Current navigator instance.
     *
     * @var NavigateInterface|NavigateEngine|NavGroup
     */
    public NavigateEngine|NavigateInterface|NavGroup $navigate;

    /**
     * Provider of the current extension.
     *
     * @var ExtendProvider
     */
    public ExtendProvider $provider;

    /**
     * NavigatorExtensionProvider constructor.
     *
     * @param  NavigateInterface  $navigate
     * @param  ExtendProvider  $provider
     */
    public function __construct(NavigateInterface $navigate, ExtendProvider $provider)
    {
        $this->navigate = $navigate;
        $this->provider = $provider;
    }

    /**
     * Provider handle for handling extension navigation.
     *
     * @return void
     */
    public function handle(): void
    {
    }

    /**
     * Add a header with title to the menu.
     *
     * @param  string  $title
     * @return NavigateEngine
     */
    public function menu_header(string $title): NavigateEngine
    {
        return $this->navigate->menu_header($title);
    }

    /**
     * Create a group of menu items.
     *
     * @param  string|null  $title
     * @param  null  $route
     * @param  array|Closure|null  $cb
     * @return NavGroup
     */
    public function group(string $title = null, $route = null, array|Closure $cb = null): NavGroup
    {
        return $this->navigate->group($title, $route, $cb);
    }

    /**
     * Add a menu item.
     *
     * @param  string|null  $title
     * @param  string|null  $route
     * @param  null  $action
     * @return NavItem
     */
    public function item(string $title = null, string $route = null, $action = null): NavItem
    {
        return $this->navigate->item($title, $route, $action);
    }

    /**
     * Add a template that will be displayed in the navigation which is in the header.
     *
     * @param  string  $view
     * @param  array  $params
     * @param  bool  $prepend
     * @return NavGroup|NavigateEngine
     */
    public function nav_bar_view(string $view, array $params = [], bool $prepend = false): NavGroup|NavigateEngine
    {
        return $this->navigate->nav_bar_view($view, $params, $prepend);
    }

    /**
     * Add a Vue component that will be displayed in the navigation which is in the header.
     *
     * @param  string  $view
     * @param  array  $params
     * @param  bool  $prepend
     * @return NavGroup|NavigateEngine
     */
    public function nav_bar_vue(string $view, array $params = [], bool $prepend = false): NavGroup|NavigateEngine
    {
        return $this->navigate->nav_bar_vue($view, $params, $prepend);
    }

    /**
     * Add a template that will be displayed in the left navigation which is in the header.
     *
     * @param  string  $view
     * @param  array  $params
     * @return NavGroup|NavigateEngine
     */
    public function left_nav_bar_view(string $view, array $params = []): NavGroup|NavigateEngine
    {
        return $this->navigate->left_nav_bar_view($view, $params);
    }

    /**
     * Add a Vue component that will be displayed in the left navigation which is in the header.
     *
     * @param  string  $view
     * @param  array  $params
     * @return NavGroup|NavigateEngine
     */
    public function left_nav_bar_vue(string $view, array $params = []): NavGroup|NavigateEngine
    {
        return $this->navigate->left_nav_bar_vue($view, $params);
    }

    /**
     * Magic method for calling all other navigator methods.
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->navigate->{$name}(...$arguments);
    }
}
