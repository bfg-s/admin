<?php

declare(strict_types=1);

namespace Admin\Core;

use Admin\Admin;
use Admin\Facades\NavigateFacade;
use Admin\Navigate;
use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Routing\Router;

class RoutesAdaptor
{
    /**
     * Create routes by menu settings.
     * @param  Router  $router
     */
    public static function create_by_menu(Router $router)
    {
        PendingResourceRegistration::macro('get_controller', function () {
            return $this->controller;
        });

        Navigate::$router = $router;

        $extensions = Admin::$nav_extensions;

        if (isset($extensions['application'])) {
            Navigate::$extension = $extensions['application'];

            $extensions['application']->navigator(NavigateFacade::instance());

            Navigate::$extension = null;

            $extensions = Admin::$nav_extensions;
        }

        if (count($extensions)) {
            foreach ($extensions as $key => $extension) {
                if ($key === 'application') {
                    continue;
                }

                if (is_array($extension)) {
                    foreach ($extension as $item) {
                        Navigate::$extension = $item;

                        $item->navigator(NavigateFacade::instance());

                        Navigate::$extension = null;
                    }
                } else {
                    Navigate::$extension = $extension;

                    $extension->navigator(NavigateFacade::instance());

                    Navigate::$extension = null;
                }
            }
        }

        $new_menu = [];

        foreach (NavigateFacade::get() as $menu) {
            static::make_route($menu, $router);
            $new_menu[] = $menu;
        }

        Navigate::$items = $new_menu;
    }

    /**
     * Recursive rout maker.
     * @param  array  $menu
     * @param  Router  $router
     */
    protected static function make_route(array &$menu, Router $router)
    {
        if (isset($menu['items']) && isset($menu['route']) && count($menu['items'])) {
            $uri = $menu['route'].(isset($menu['uri']) ? ('/'.trim($menu['uri'], '/')) : '');

            $router->as($uri.'.')->prefix($uri)->middleware($menu['middleware'] ?? [])->group(static function (
                Router $router
            ) use ($menu) {
                foreach ($menu['items'] as $item) {
                    static::make_route($item, $router);

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

                static::make_post($router, $uri, $menu['post'], $menu['route']);
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
                static::make_post($router, $uri, $menu['post'], $menu['route']);
            }

            if (isset($menu['delete'])) {
                static::make_delete($router, $uri, $menu['delete'], $menu['route']);
            }
        }
    }

    /**
     * @param  Router  $router
     * @param $uri
     * @param $action
     * @param  string  $name
     */
    protected static function make_post(Router $router, $uri, $action, string $name)
    {
        if (is_array($action) && isset($action['uri'])) {
            $uri = trim($uri, '/').'/'.trim($action['uri'], '/');
            unset($action['uri']);
        }

        $router->post($uri, $action)->name($name.'.post');
    }

    /**
     * @param  Router  $router
     * @param $uri
     * @param $action
     * @param  string  $name
     */
    protected static function make_delete(Router $router, $uri, $action, string $name)
    {
        if (is_array($action) && isset($action['uri'])) {
            $uri = trim($uri, '/').'/'.trim($action['uri'], '/');
            unset($action['uri']);
        }

        $router->delete($uri, $action)->name($name.'.destroy');
    }
}
