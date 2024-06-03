<?php

declare(strict_types=1);

namespace Admin\Core;

use Admin\AdminEngine;
use Admin\Facades\Navigate;
use Admin\NavigateEngine;
use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Routing\Router;

/**
 * Part of the kernel that is responsible for adapting navigation to routes.
 */
class RoutesAdaptor
{
    /**
     * Create routes by menu settings.
     *
     * @param  Router  $router
     */
    public static function createByMenu(Router $router): void
    {
        PendingResourceRegistration::macro('get_controller', function () {
            return $this->controller;
        });

        NavigateEngine::$router = $router;

        $extensions = AdminEngine::$nav_extensions;

        if (isset($extensions['application'])) {
            NavigateEngine::$extension = $extensions['application'];

            $extensions['application']->navigator(Navigate::instance());

            NavigateEngine::$extension = null;

            $extensions = AdminEngine::$nav_extensions;
        }

        if (count($extensions)) {
            foreach ($extensions as $key => $extension) {
                if ($key === 'application') {
                    continue;
                }

                if (is_array($extension)) {
                    foreach ($extension as $item) {
                        NavigateEngine::$extension = $item;

                        $item->navigator(Navigate::instance());

                        NavigateEngine::$extension = null;
                    }
                } else {
                    NavigateEngine::$extension = $extension;

                    $extension->navigator(Navigate::instance());

                    NavigateEngine::$extension = null;
                }
            }
        }

        $new_menu = [];

        foreach (Navigate::get() as $menu) {

            static::makeDeepRoute($menu, $router);

            $new_menu[] = $menu;
        }

        NavigateEngine::$items = $new_menu;
    }

    /**
     * Recursive rout maker.
     *
     * @param  array  $menu
     * @param  Router  $router
     */
    protected static function makeDeepRoute(array &$menu, Router $router): void
    {
        if (isset($menu['items']) && isset($menu['route']) && count($menu['items'])) {
            $uri = $menu['route'].(isset($menu['uri']) ? ('/'.trim($menu['uri'], '/')) : '');

            $router->as($uri.'.')->prefix($uri)->middleware($menu['middleware'] ?? [])->group(static function (
                Router $router
            ) use ($menu) {
                foreach ($menu['items'] as $item) {
                    static::makeDeepRoute($item, $router);

                    if (isset($menu['router']) && is_array($menu['router'])) {
                        foreach ($menu['router'] as $r) {
                            if (is_embedded_call($r)) {
                                call_user_func($r, $router);
                            }
                        }
                    }
                }
            });
        } elseif (isset($menu['resource']) && is_array($menu['resource']) && isset($menu['resource']['action']) && isset($menu['resource']['name'])) {
            $action = $menu['resource']['action'];
            $name = $menu['resource']['name'];

            $r = null;

            if (!isset($menu['ignored'])) {
                $r = $router->resource($name, $action, $menu['resource']['options'])
                    ->middleware($menu['middleware'] ?? []);
            }

            if (isset($menu['router']) && is_array($menu['router'])) {
                foreach ($menu['router'] as $r) {
                    if (is_embedded_call($r)) {
                        call_user_func($r, $router);
                    }
                }
            }

            if (isset($menu['where']) && $r) {
                $r->where($menu['where']);
            }

            if (isset($menu['post']) && isset($menu['route'])) {
                $uri = $menu['route'];

                static::makeRoutePost($router, $uri, $menu['post'], $menu['route']);
            }
        } elseif ((isset($menu['action']) || isset($menu['view'])) && isset($menu['route'])) {
            $method = isset($menu['view']) ? 'view' : ($menu['method'] ?? 'get');
            $action = $menu['view'] ?? $menu['action'];
            $uri = $menu['route'].(isset($menu['uri']) ? ('/'.trim($menu['uri'], '/')) : '');

            if (is_array($action) && isset($action['uri'])) {
                $uri = trim($uri, '/').'/'.trim($action['uri'], '/');
                unset($action['uri']);
            }

            if (!isset($menu['ignored'])) {
                $r = $router->{$method}($uri, $action)->name($menu['route'])
                    ->middleware($menu['middleware'] ?? []);
            }

            if (isset($menu['router']) && is_array($menu['router'])) {
                foreach ($menu['router'] as $r) {
                    if (is_embedded_call($r)) {
                        call_user_func($r, $router);
                    }
                }
            }

            if (isset($menu['where']) && $r) {
                $r->where($menu['where']);
            }

            if (isset($menu['post'])) {
                static::makeRoutePost($router, $uri, $menu['post'], $menu['route']);
            }

            if (isset($menu['delete'])) {
                static::makeRouteDelete($router, $uri, $menu['delete'], $menu['route']);
            }
        }
    }

    /**
     * Make POST route if needed.
     *
     * @param  Router  $router
     * @param $uri
     * @param $action
     * @param  string  $name
     */
    protected static function makeRoutePost(Router $router, $uri, $action, string $name): void
    {
        if (is_array($action) && isset($action['uri'])) {
            $uri = trim($uri, '/').'/'.trim($action['uri'], '/');
            unset($action['uri']);
        }

        $router->post($uri, $action)->name($name.'.post');
    }

    /**
     * Make DELETE route if needed.
     *
     * @param  Router  $router
     * @param $uri
     * @param $action
     * @param  string  $name
     */
    protected static function makeRouteDelete(Router $router, $uri, $action, string $name): void
    {
        if (is_array($action) && isset($action['uri'])) {
            $uri = trim($uri, '/').'/'.trim($action['uri'], '/');
            unset($action['uri']);
        }

        $router->delete($uri, $action)->name($name.'.destroy');
    }
}
