<?php

namespace Lar\LteAdmin\Getters;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Lar\Developer\Getter;
use Lar\LteAdmin\Exceptions\ShouldBeModelInControllerException;
use Lar\LteAdmin\ExtendProvider;
use Lar\LteAdmin\Models\LtePermission;
use Navigate;
use Route;
use Str;

class Menu extends Getter
{
    /**
     * @var string
     */
    public static $name = 'lte.menu';

    /**
     * @var int
     */
    protected static $nested_counter = 0;

    protected static $currentQueryField = null;
    protected static $queries = [];

    public static function saveCurrentQuery()
    {
        $can = request()->pjax() || !request()->ajax();
        if (request()->isMethod('GET') && $can) {
            $name = static::currentQueryField();
            if (isset(static::$queries[$name]) && static::$queries[$name]) {
                return;
            }
            $all = request()->query();
            foreach ($all as $key => $item) {
                if (str_starts_with($key, '_')) {
                    unset($all[$key]);
                }
            }
            static::$queries[$name] = $all;
            session([$name => $all]);
        }
    }

    protected static function currentQueryField()
    {
        if (!static::$currentQueryField) {
            static::$currentQueryField = Route::currentRouteName();
        }

        return static::$currentQueryField;
    }

    public static function getCurrentQuery()
    {
        return static::getQuery(static::currentQueryField());
    }

    public static function getQuery(string $name)
    {
        if (!isset(static::$queries[$name]) || !static::$queries[$name]) {
            static::$queries[$name] = session($name, []);
        }

        return static::$queries[$name];
    }

    /**
     * @return Collection
     */
    public static function all()
    {
        return collect(config('lte_menu'));
    }

    /**
     * @return array|null
     */
    public static function now()
    {
        $return = gets()->lte->menu->nested_collect->where('route', '=', static::currentQueryField())->first();
        if (!$return) {
            $route = preg_replace('/\.[a-zA-Z0-9\_\-]+$/', '', static::currentQueryField());
            $return = gets()->lte->menu->nested_collect->where('route', '=', $route)->first();
        }

        return $return;
    }

    /**
     * @return string|null
     */
    public static function type()
    {
        $return = null;

        $menu = gets()->lte->menu->now;

        if ($menu && isset($menu['current.type'])) {
            $return = $menu['current.type'];

            if ($return === 'store') {
                $return = 'create';
            } elseif ($return === 'update') {
                $return = 'edit';
            }
        }

        return $return;
    }

    /**
     * @param  null  $__name_
     * @param  null  $__default
     * @return string|null
     */
    public static function data($__name_ = null, $__default = null)
    {
        $return = $__default;

        $menu = gets()->lte->menu->now;

        if ($menu && isset($menu['data'])) {
            $return = $menu['data'];

            if ($__name_) {
                $return = isset($return[$__name_]) ? $return[$__name_] : $__default;
            }
        }

        return $return;
    }

    /**
     * @return object|string|null
     */
    public static function model_primary()
    {
        $menu = gets()->lte->menu->now;

        if (Route::current() && isset($menu['model.param'])) {
            return Route::current()->parameter($menu['model.param']);
        }

        return null;
    }

    /**
     * @return Model|string|null
     * @throws ShouldBeModelInControllerException
     */
    public static function model()
    {
        $return = null;

        $menu = gets()->lte->menu->now;

        $current = Route::current();

        if ($current->controller) {
            if (method_exists($current->controller, 'getModel')) {
                $return = call_user_func([$current->controller, 'getModel']);
            } elseif (property_exists($current->controller, 'model')) {
                $controller = $current->controller;
                $return = $controller::$model;
            } else {
                throw new ShouldBeModelInControllerException();
            }
        }

        if (is_string($return) && class_exists($return)) {
            /** @var Model $return */
            $return = new $return;
        }

        $pm = $menu['model.param'] ?? Str::singular(Str::snake(class_basename($return)));

        if (
            $pm &&
            $return instanceof Model &&
            !$return->exists &&
            Route::current()->hasParameter($pm)
        ) {
            if ($find = $return->where($return->getRouteKeyName(), Route::current()->parameter($pm))->first()) {
                $return = $find;
            }
        }

        return $return;
    }

    /**
     * @return Collection|array
     */
    public static function now_parents()
    {
        return gets()->lte->menu->now ? collect(static::get_parents(gets()->lte->menu->now)) : collect([]);
    }

    /**
     * @param  array  $subject
     * @param  array  $result
     * @return array
     */
    protected static function get_parents(array $subject, $result = [])
    {
        $result[$subject['id']] = $subject;

        if ($subject['parent_id']) {
            $parent = gets()->lte->menu->nested_collect->where('active', true)->where('id',
                $subject['parent_id'])->first();

            if ($parent) {
                return static::get_parents($parent, $result);
            } else {
                return $result;
            }
        }

        return $result;
    }

    /**
     * @return Collection
     */
    public static function nested_collect()
    {
        return collect(gets()->lte->menu->nested);
    }

    /**
     * @param  array  $params
     * @return bool|string
     */
    public static function current_index_link(array $params = [])
    {
        $menu = gets()->lte->menu->now;

        if (isset($menu['link.index'])) {
            return $menu['link.index']($params);
        }

        return false;
    }

    /**
     * @param  string|int|array  $params
     * @return bool|string
     */
    public static function current_show_link($params)
    {
        $menu = gets()->lte->menu->now;

        if (isset($menu['link.show'])) {
            return $menu['link.show']($params);
        }

        return false;
    }

    /**
     * @param  string|int|array  $params
     * @return bool|string
     */
    public static function current_update_link($params)
    {
        $menu = gets()->lte->menu->now;

        if (isset($menu['link.update'])) {
            return $menu['link.update']($params);
        }

        return false;
    }

    /**
     * @param  string|int|array  $params
     * @return bool|string
     */
    public static function current_destroy_link($params)
    {
        $menu = gets()->lte->menu->now;

        if (isset($menu['link.destroy'])) {
            return $menu['link.destroy']($params);
        }

        return false;
    }

    /**
     * @param  string|int|array  $params
     * @return bool|string
     */
    public static function current_edit_link($params)
    {
        $menu = gets()->lte->menu->now;

        if (isset($menu['link.edit'])) {
            return $menu['link.edit']($params);
        }

        return false;
    }

    /**
     * @param  array  $params
     * @return bool|string
     */
    public static function current_store_link(array $params = [])
    {
        $menu = gets()->lte->menu->now;

        if (isset($menu['link.store'])) {
            return $menu['link.store']($params);
        }

        return false;
    }

    /**
     * @param  array  $params
     * @return bool|string
     */
    public static function current_create_link(array $params = [])
    {
        $menu = gets()->lte->menu->now;

        if (isset($menu['link.create'])) {
            return $menu['link.create']($params);
        }

        return false;
    }

    /**
     * @param  bool  $__route_items_
     * @param  int  $__route_parent_id_
     * @param  string  $__route_name_
     * @param  array|null  $__parent
     * @return array
     */
    public static function nested(
        $__route_items_ = false,
        int $__route_parent_id_ = 0,
        $__route_name_ = 'lte',
        array $__parent = null
    ) {
        if ($__route_items_ === false) {
            $__route_items_ = Navigate::getMaked();
        }

        $return = [];

        foreach ($__route_items_ as $key => $item) {
            $childs = false;

            if (isset($item['items'])) {
                $childs = $item['items'];
                unset($item['items']);
            }

            $id = static::$nested_counter;

            $add = [
                'id' => $id,
                'parent_id' => $__route_parent_id_,
            ];

            if ($__parent && isset($__parent['roles'])) {
                $item['roles'] = $__parent['roles'];
            }

            if (!isset($item['route'])) {
                $item['route'] = false;
            } elseif ($__route_name_) {
                if (str_replace(['{', '?', '}'], '', $item['route']) !== $item['route']) {
                    $item['route'] = $__route_name_;
                } else {
                    $item['route'] = $__route_name_.'.'.(isset($item['resource']['name']) ? str_replace('/', '.',
                            $item['resource']['name']) : $item['route']);
                }
            }

            $item['target'] = false;

            if (!isset($item['link'])) {
                $item['link'] = false;
            } elseif (preg_match('/^http/', $item['link'])) {
                $item['target'] = true;
            }

            $item['current.type'] = null;

            if (isset($item['model']) && !isset($item['model.param'])) {
                $item['model.param'] = Str::singular(Str::snake(class_basename($item['model'])));
            }

            if (isset($item['link_params'])) {
                $item['route_params'] = array_merge($item['route_params'] ?? [], call_user_func($item['link_params']));
            }

            if (isset($item['action'])) {
                $item['route_params'] = array_merge($item['route_params'] ?? [], static::getQuery($item['route']));
                $item['link'] = route($item['route'], $item['route_params'] ?? []);
                $item['controller'] = ltrim(is_array($item['action']) ? $item['action'][0] : Str::parseCallback($item['action'])[0],
                    '\\');
                $item['current'] = $item['route'] == static::currentQueryField();
            } elseif (isset($item['resource'])) {
                $item['route_params'] = array_callable_results($item['route_params'] ?? [], $item);

                $item['current.type'] = str_replace($item['route'].'.', '', static::currentQueryField());
                $item['current'] = str_replace('.'.$item['current.type'], '',
                        static::currentQueryField()) == $item['route'];

                $item['link.show'] = function ($params) use ($item) {
                    if (
                        (isset($item['resource_only']) && !in_array('show', $item['resource_only']))
                        || (isset($item['resource_except']) && in_array('show', $item['resource_except']))
                    ) {
                        return null;
                    }
                    if (!is_array($params) && isset($item['model.param'])) {
                        $params = [$item['model.param'] => $params];
                    }
                    $name = $item['route'].'.show';
                    $params = array_merge($params, static::getQuery($name));

                    return route($name, array_merge($params, ($item['route_params'] ?? [])));
                };
                $item['link.update'] = function ($params) use ($item) {
                    if (
                        (isset($item['resource_only']) && !in_array('update', $item['resource_only']))
                        || (isset($item['resource_except']) && in_array('update', $item['resource_except']))
                    ) {
                        return null;
                    }
                    if (!is_array($params) && isset($item['model.param'])) {
                        $params = [$item['model.param'] => $params];
                    }

                    return route($item['route'].'.update', array_merge($params, ($item['route_params'] ?? [])));
                };
                $item['link.destroy'] = function ($params) use ($item) {
                    if (
                        (isset($item['resource_only']) && !in_array('destroy', $item['resource_only']))
                        || (isset($item['resource_except']) && in_array('destroy', $item['resource_except']))
                    ) {
                        return null;
                    }
                    if (!is_array($params) && isset($item['model.param'])) {
                        $params = [$item['model.param'] => $params];
                    }

                    return route($item['route'].'.destroy', array_merge($params, ($item['route_params'] ?? [])));
                };
                $item['link.edit'] = function ($params) use ($item) {
                    if (
                        (isset($item['resource_only']) && !in_array('edit', $item['resource_only']))
                        || (isset($item['resource_except']) && in_array('edit', $item['resource_except']))
                    ) {
                        return null;
                    }
                    if (!is_array($params) && isset($item['model.param'])) {
                        $params = [$item['model.param'] => $params];
                    }

                    $name = $item['route'].'.edit';
                    $params = array_merge($params, static::getQuery($name));

                    return route($name, array_merge($params, ($item['route_params'] ?? [])));
                };
                $item['link.index'] = function (array $params = []) use ($item) {
                    if (
                        (isset($item['resource_only']) && !in_array('index', $item['resource_only']))
                        || (isset($item['resource_except']) && in_array('index', $item['resource_except']))
                    ) {
                        return null;
                    }

                    $name = $item['route'].'.index';
                    $params = array_merge($params, static::getQuery($name));

                    return route($name, array_merge($params, ($item['route_params'] ?? [])));
                };
                $item['link.store'] = function (array $params = []) use ($item) {
                    if (
                        (isset($item['resource_only']) && !in_array('store', $item['resource_only']))
                        || (isset($item['resource_except']) && in_array('store', $item['resource_except']))
                    ) {
                        return null;
                    }
                    $name = $item['route'].'.store';

                    return route($name, array_merge($params, ($item['route_params'] ?? [])));
                };
                $item['link.create'] = function (array $params = []) use ($item) {
                    if (
                        (isset($item['resource_only']) && !in_array('create', $item['resource_only']))
                        || (isset($item['resource_except']) && in_array('create', $item['resource_except']))
                    ) {
                        return null;
                    }
                    $name = $item['route'].'.create';
                    $params = array_merge($params, static::getQuery($name));

                    return route($name, array_merge($params, ($item['route_params'] ?? [])));
                };
                $item['link'] = $item['link.index']();
                $item['controller'] = ltrim($item['resource']['action'], '\\');
            }

            /** @var string $model */
            $item['model_class'] = ($item['controller'] ?? null) ? ($item['controller']::$model ?? null) : null;

            if (!isset($item['selected'])) {
                $item['selected'] = false;
            }

            if (!$item['selected'] && $item['route']) {
                $current_route = static::currentQueryField();

                $item['selected'] = $item['route'] == $current_route
                    || Str::is($item['route'].'.*', $current_route);
            } elseif (!$item['selected'] && $item['link'] && !$item['target']) {
                $link = trim($item['link'], '/');
                $link = ltrim($link, App::getLocale());
                $link = trim($link, '/');
                $path = ltrim(request()->decodedPath().'/', App::getLocale());
                $path = trim($path, '/');

                $item['link'] = '/'.App::getLocale().'/'.$link;

                $item['selected'] = Str::is($link.'*', $path);
            }

            if (!isset($item['active'])) {
                $item['active'] = isset($item['title']);
            } elseif ($item['active'] && !isset($item['title'])) {
                $item['title'] = false;
            }

            if (isset($item['link']) && $item['link'] && isset($item['active']) && $item['active']) {
                $item['active'] = LtePermission::checkUrl($item['link']);
            }

            if (isset($item['extension']) && $item['extension'] && $item['active']) {
                /** @var ExtendProvider $extension */
                $extension = $item['extension'];
                if ($extension::$roles && $extension::$roles->count()) {
                    $roles = $extension::$roles->pluck('slug')->toArray();
                    $item['active'] = lte_user()->hasRoles($roles);
                }
            }

            $result = array_merge($add, $item);

            $return[] = $result;

            $last = array_key_last($return);

            static::$nested_counter++;

            if ($childs) {
                $chl = static::nested($childs, $id, $item['route'] ?? 'lte', $result);

                $return[$last]['active'] = (bool) collect($chl)->where('active', true)->count();

                $return = array_merge($return, $chl);
            }
        }

        return $return;
    }

    public static function collapse($item)
    {
    }

    /**
     * @return Collection
     */
    public function default()
    {
        return collect([]);
    }
}
