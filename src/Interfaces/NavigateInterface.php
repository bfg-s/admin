<?php

namespace Lar\LteAdmin\Interfaces;

/**
 * Interface NavigateInterface
 * @package Lar\LteAdmin\Interfaces
 */
interface NavigateInterface {

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
     * @return \Lar\LteAdmin\Core\NavGroup
     */
    public function item(string $title = null, string $route = null, $action = null);
}