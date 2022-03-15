<?php

namespace LteAdmin\Repositories;

use App;
use Bfg\Repository\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LteAdmin\Exceptions\ResourceControllerExistsException;
use LteAdmin\Exceptions\ShouldBeModelInControllerException;
use LteAdmin\Models\LtePermission;
use LteAdmin\Models\LteUser;
use Navigate;
use Route;

/**
 * @property-read array $nested
 * @property-read string|null $currentQueryField
 * @property-read string|null $type
 * @property-read array $getCurrentQuery
 * @property-read array $now
 * @property-read array|null $data
 * @property-read mixed $modelPrimary
 * @property-read mixed $currentController
 * @property-read Collection $nestedCollect
 * @property-read Collection $nowParents
 * @property-read Model $modelNow
 * @property-read null $saveCurrentQuery
 */
class AdminRepository extends Repository
{
    protected static array $cache = [
        'models' => [],
        'queries' => [],
        'nested_counter' => 0,
    ];

    public function now()
    {
        $return = $this->nestedCollect->where('route', '=', $this->currentQueryField)->first();
        if (!$return) {
            $route = preg_replace('/\.[a-zA-Z0-9_\-]+$/', '', $this->currentQueryField);
            $return = $this->nestedCollect->where('route', '=', $route)->first();
        }

        return $return;
    }

    public function type()
    {
        $return = null;

        $menu = $this->now;

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

    public function data($__name_ = null, $__default = null)
    {
        $return = $__default;

        $menu = $this->now;

        if ($menu && isset($menu['data'])) {
            $return = $menu['data'];

            if ($__name_) {
                $return = $return[$__name_] ?? $__default;
            }
        }

        return $return;
    }

    public function modelPrimary(): object|string|null
    {
        $menu = $this->now;

        if (Route::current() && isset($menu['model.param'])) {
            return Route::current()->parameter($menu['model.param']);
        }

        return null;
    }

    public function modelNow()
    {
        $return = null;

        $menu = $this->now;

        if ($controller = $this->currentController) {
            if (method_exists($controller, 'getModel')) {
                $return = call_user_func([$controller, 'getModel']);
            } elseif (property_exists($controller, 'model')) {
                $return = $controller::$model;
            } else {
                throw new ShouldBeModelInControllerException();
            }
        }

        if (is_string($return) && class_exists($return)) {
            /** @var Model $return */
            $return = new $return();
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

    public function saveCurrentQuery(Request $request)
    {
        $can = $request->pjax() || !$request->ajax();
        if ($request->isMethod('GET') && $can) {
            $name = $this->currentQueryField;
            if (isset(static::$cache['queries'][$name]) && static::$cache['queries'][$name]) {
                return;
            }
            $all = $request->query();
            foreach ($all as $key => $item) {
                if (str_starts_with($key, '_')) {
                    unset($all[$key]);
                }
            }
            static::$cache['queries'][$name] = $all;
            session([$name => $all]);
        }
    }

    public function getCurrentQuery()
    {
        return $this->getQuery($this->currentQueryField);
    }

    public function getQuery(string $name)
    {
        if (!isset(static::$cache['queries'][$name]) || !static::$cache['queries'][$name]) {
            static::$cache['queries'][$name] = session($name, []);
        }

        return static::$cache['queries'][$name];
    }

    public function currentQueryField(): ?string
    {
        return Route::currentRouteName();
    }

    public function currentController(): mixed
    {
        return Route::current()?->controller;
    }

    public function nestedCollect(): Collection
    {
        return collect($this->nested);
    }

    public function nowParents(): Collection
    {
        return $this->now ? collect($this->getParents($this->now)) : collect([]);
    }

    protected function getParents(array $subject, $result = []): array
    {
        $result[$subject['id']] = $subject;

        if ($subject['parent_id']) {
            $parent = $this->nestedCollect->where('active', true)->where(
                'id',
                $subject['parent_id']
            )->first();

            if ($parent) {
                return $this->getParents($parent, $result);
            } else {
                return $result;
            }
        }

        return $result;
    }

    public function nested(
        $__route_items_ = false,
        int $__route_parent_id_ = 0,
        $__route_name_ = 'lte',
        array $__parent = null
    ): array {
        if ($__route_items_ === false) {
            $__route_items_ = Navigate::getMaked();
        }

        $return = [];

        foreach ($__route_items_ as $key => $item) {
            $child = false;

            if (isset($item['items'])) {
                $child = $item['items'];
                unset($item['items']);
            }

            $id = static::$cache['nested_counter'];

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
                    $item['route'] = $__route_name_.'.'.(isset($item['resource']['name']) ? str_replace(
                            '/',
                            '.',
                            $item['resource']['name']
                        ) : $item['route']);
                }
            }

            $item['target'] = false;

            if (!isset($item['link'])) {
                $item['link'] = false;
            } elseif (str_starts_with($item['link'], 'http')) {
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
                $item['route_params'] = array_merge($item['route_params'] ?? [], $this->getQuery($item['route']));
                $item['link'] = route($item['route'], $item['route_params'] ?? []);
                $item['controller'] = ltrim(
                    is_array($item['action']) ? $item['action'][0] : Str::parseCallback($item['action'])[0],
                    '\\'
                );
                $item['current'] = $item['route'] == $this->currentQueryField;
            } elseif (isset($item['resource'])) {
                $item['route_params'] = array_callable_results($item['route_params'] ?? [], $item);

                $item['current.type'] = str_replace($item['route'].'.', '', $this->currentQueryField);
                $item['current'] = str_replace(
                        '.'.$item['current.type'],
                        '',
                        $this->currentQueryField
                    ) == $item['route'];

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
                    $params = array_merge($params, $this->getQuery($name));

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
                    $params = array_merge($params, $this->getQuery($name));

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
                    $params = array_merge($params, $this->getQuery($name));

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
                    $params = array_merge($params, $this->getQuery($name));

                    return route($name, array_merge($params, ($item['route_params'] ?? [])));
                };
                $item['link'] = $item['link.index']();
                $item['controller'] = ltrim($item['resource']['action'], '\\');
            }

            /** @var string $model */
            $item['model_class'] = ($item['controller'] ?? null) ? ($item['controller']::$model ?? null) : null;

            if ($item['model_class'] && in_array($item['model_class'], static::$cache['models'])) {
                throw new ResourceControllerExistsException($item['model_class'], $item['controller']);
            } else {
                static::$cache['models'][] = $item['model_class'];
            }

            if (!isset($item['selected'])) {
                $item['selected'] = false;
            }

            if (!$item['selected'] && $item['route']) {
                $current_route = $this->currentQueryField;

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

            if (isset($item['link']) && $item['link'] && $item['active']) {
                $item['active'] = LtePermission::checkUrl($item['link']);
            }

            $result = array_merge($add, $item);

            $return[] = $result;

            $last = array_key_last($return);

            static::$cache['nested_counter']++;

            if ($child) {
                $chl = $this->nested($child, $id, $item['route'] ?? 'lte', $result);

                $return[$last]['active'] = (bool) collect($chl)->where('active', true)->count();

                $return = array_merge($return, $chl);
            }
        }

        return $return;
    }

    protected function getModelClass(): string
    {
        return LteUser::class;
    }
}
