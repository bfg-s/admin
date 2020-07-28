<?php

namespace Lar\LteAdmin\Interfaces;

/**
 * Interface NavigateInterface
 * @package Lar\LteAdmin\Interfaces
 */
interface ControllerSegment {

    /**
     * @param  string|null  $title
     * @param  null  $route
     * @param  \Closure|null  $cb
     * @return \Lar\LteAdmin\Core\NavGroup
     */
    public function group(string $title = null, $route = null, \Closure $cb = null);

    /**
     * @param  string|null  $title
     * @param  string|null  $route
     * @param  null  $action
     * @return \Lar\LteAdmin\Core\NavItem
     */
    public function item(string $title = null, string $route = null, $action = null);

    /**
     * @param  string  $view
     * @param  array  $params
     * @param  bool  $prepend
     * @return \Lar\LteAdmin\Core\NavGroup|\Lar\LteAdmin\Navigate
     */
    public function nav_bar_view(string $view, array $params = [], bool $prepend = false);

    /**
     * @param  string  $view
     * @param  array  $params
     * @return \Lar\LteAdmin\Core\NavGroup|\Lar\LteAdmin\Navigate
     */
    public function left_nav_bar_view(string $view, array $params = []);
}