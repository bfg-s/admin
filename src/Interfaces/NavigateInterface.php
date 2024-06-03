<?php

declare(strict_types=1);

namespace Admin\Interfaces;

use Admin\Core\NavGroup;
use Admin\Core\NavItem;
use Admin\NavigateEngine;
use Closure;

/**
 * Admin panel navigator interface.
 */
interface NavigateInterface
{
    /**
     * Add a group to a navigation group.
     *
     * @param  string|null  $title
     * @param  null  $route
     * @param  array|Closure|null  $cb
     * @return NavGroup
     */
    public function group(string $title = null, $route = null, array|Closure $cb = null): NavGroup;

    /**
     * Add an item to the navigation group.
     *
     * @param  string|null  $title
     * @param  string|null  $route
     * @param  null  $action
     * @return NavItem
     */
    public function item(string $title = null, string $route = null, $action = null): NavItem;

    /**
     * Add a navbar template.
     *
     * @param  string  $view
     * @param  array  $params
     * @param  bool  $prepend
     * @return NavGroup|NavigateEngine
     */
    public function nav_bar_view(string $view, array $params = [], bool $prepend = false): NavGroup|NavigateEngine;

    /**
     * Add left a navbar template.
     *
     * @param  string  $view
     * @param  array  $params
     * @return NavGroup|NavigateEngine
     */
    public function left_nav_bar_view(string $view, array $params = []): NavGroup|NavigateEngine;
}
