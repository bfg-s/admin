<?php

namespace Lar\LteAdmin\Interfaces;

use Closure;
use Lar\LteAdmin\Core\NavGroup;
use Lar\LteAdmin\Core\NavItem;
use Lar\LteAdmin\Navigate;

interface NavigateInterface
{
    /**
     * @param  string|null  $title
     * @param  null  $route
     * @param  Closure|array|null  $cb
     * @return NavGroup
     */
    public function group(string $title = null, $route = null, $cb = null);

    /**
     * @param  string|null  $title
     * @param  string|null  $route
     * @param  null  $action
     * @return NavItem
     */
    public function item(string $title = null, string $route = null, $action = null);

    /**
     * @param  string  $view
     * @param  array  $params
     * @param  bool  $prepend
     * @return NavGroup|Navigate
     */
    public function nav_bar_view(string $view, array $params = [], bool $prepend = false);

    /**
     * @param  string  $view
     * @param  array  $params
     * @return NavGroup|Navigate
     */
    public function left_nav_bar_view(string $view, array $params = []);
}
