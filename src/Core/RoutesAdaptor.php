<?php

namespace Lar\LteAdmin\Core;

use Illuminate\Routing\PendingResourceRegistration;
use Lar\LteAdmin\LteAdmin;
use Lar\LteAdmin\Navigate;
use Lar\Roads\Roads;

/**
 * Class RoutesAdaptor
 * @package Lar\LteAdmin\Core
 */
class RoutesAdaptor
{
    /**
     * Create routes by menu settings
     * @param  Roads  $roads
     */
    public static function create_by_menu(Roads $roads)
    {
        PendingResourceRegistration::macro('get_controller', function () { return $this->controller; });

        Navigate::$roads = $roads;

        $extensions = LteAdmin::$nav_extensions;

        if (isset($extensions['application'])) {

            Navigate::$extension = $extensions['application'];

            $extensions['application']->navigator(\Navigate::instance());

            Navigate::$extension = null;

            $extensions = LteAdmin::$nav_extensions;
        }

        if (count($extensions)) {

            foreach ($extensions as $key => $extension) {

                if ($key === 'application') {
                    continue;
                }

                if (is_array($extension)) {

                    foreach ($extension as $item) {

                        Navigate::$extension = $item;

                        $item->navigator(\Navigate::instance());

                        Navigate::$extension = null;
                    }

                }
                else {

                    Navigate::$extension = $extension;

                    $extension->navigator(\Navigate::instance());

                    Navigate::$extension = null;
                }
            }
        }

        $new_menu = [];

        foreach (\Navigate::get() as $menu) {

            static::make_route($menu, $roads);
            $new_menu[] = $menu;
        }

        Navigate::$items = $new_menu;
    }

    /**
     * Recursive rout maker
     * @param  array  $menu
     * @param  Roads  $roads
     */
    protected static function make_route(array &$menu, Roads $roads) {

        if (isset($menu['items']) && isset($menu['route']) && count($menu['items'])) {

            $uri = $menu['route'] . (isset($menu['uri']) ? ('/' . trim($menu['uri'], '/')) : '');

            $roads->asx($uri)->middleware($menu['middleware'] ?? [])->group(function (Roads $roads) use ($menu) {

                foreach ($menu['items'] as $item) {

                    static::make_route($item, $roads);

                    if (isset($menu['router']) && is_array($menu['router'])) {
                        foreach ($menu['router'] as $r) {
                            if (is_embedded_call($r)) { call_user_func($r, $roads); }
                        }
                    }
                }
            });
        }

        else if (isset($menu['resource']) && is_array($menu['resource']) && isset($menu['resource']['action']) && isset($menu['resource']['name'])) {

            $action = $menu['resource']['action'];
            $name = $menu['resource']['name'];

            $r = null;

            if (!isset($menu['ignored'])) {

                $r = $roads->resource($name, $action, $menu['resource']['options'])
                    ->middleware($menu['middleware'] ?? []);

                $ns = collect(app('router')->getGroupStack())->reverse()->implode('namespace', '\\');

                if (strpos($r->get_controller(), '\\') === false) {
                    $controller = trim($ns, '\\') . "\\" . $r->get_controller();
                } else {
                    $controller = trim($r->get_controller(), '\\');
                }

                $menu['controller'] = $controller;
            }

            if (isset($menu['router']) && is_array($menu['router'])) {
                foreach ($menu['router'] as $r) {
                    if (is_embedded_call($r)) { call_user_func($r, $roads); }
                }
            }

            if (isset($menu['where']) && $r) {
                $r->where($menu['where']);
            }

            if (isset($menu['post']) && isset($menu['route'])) {

                $uri = $menu['route'];

                static::make_post($roads, $uri, $menu['post'], $menu['route']);
            }
        }

        else if ((isset($menu['action']) || isset($menu['view'])) && isset($menu['route'])) {

            $method = isset($menu['view']) ? 'view' : ($menu['method'] ?? 'get');
            $action = $menu['view'] ?? $menu['action'];
            $uri = $menu['route'] . (isset($menu['uri']) ? ('/' . trim($menu['uri'], '/')) : '');


            if (is_array($action) && isset($action['uri'])) {

                $uri = trim($uri, '/') . '/' . trim($action['uri'], '/');
                unset($action['uri']);
            }

            if (!isset($menu['ignored'])) {

                $r = $roads->{$method}($uri, $action)->name($menu['route'])
                    ->middleware($menu['middleware'] ?? []);

                $ns = collect(app('router')->getGroupStack())->reverse()->implode('namespace', '\\');
                $controller = \Str::parseCallback($r->getAction('controller'))[0];

                if (strpos($controller, '\\') === false) {
                    $controller = trim($ns, '\\') . "\\" . $controller;
                } else {
                    $controller = trim($controller, '\\');
                }

                $menu['controller'] = $controller;
            }

            if (isset($menu['router']) && is_array($menu['router'])) {
                foreach ($menu['router'] as $r) {
                    if (is_embedded_call($r)) { call_user_func($r, $roads); }
                }
            }

            if (isset($menu['where']) && $r) {
                $r->where($menu['where']);
            }

            if (isset($menu['post'])) {

                static::make_post($roads, $uri, $menu['post'], $menu['route']);
            }

            if (isset($menu['delete'])) {

                static::make_delete($roads, $uri, $menu['delete'], $menu['route']);
            }
        }
    }

    /**
     * @param  Roads  $roads
     * @param $uri
     * @param $action
     * @param  string  $name
     */
    protected static function make_post(Roads $roads, $uri, $action, string $name)
    {
        if (is_array($action) && isset($action['uri'])) {

            $uri = trim($uri, '/') . '/' . trim($action['uri'], '/');
            unset($action['uri']);
        }

        $roads->post($uri, $action)->name($name . '.post');
    }

    /**
     * @param  Roads  $roads
     * @param $uri
     * @param $action
     * @param  string  $name
     */
    protected static function make_delete(Roads $roads, $uri, $action, string $name)
    {
        if (is_array($action) && isset($action['uri'])) {

            $uri = trim($uri, '/') . '/' . trim($action['uri'], '/');
            unset($action['uri']);
        }

        $roads->delete($uri, $action)->name($name . '.destroy');
    }
}
