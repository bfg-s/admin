<?php

namespace Lar\LteAdmin\Getters;

use Illuminate\Database\Eloquent\Model;
use Lar\Developer\Getter;
use Lar\LteAdmin\Models\LtePermission;

/**
 * Class Menu
 * 
 * @package Lar\LteAdmin\Getters
 */
class Menu extends Getter
{
    /**
     * @var string
     */
    public static $name = "lte.menu";

    /**
     * @var int
     */
    protected static $nested_counter = 0;

    /**
     * @return \Illuminate\Support\Collection
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
        $return = gets()->lte->menu->nested_collect->where('route', '=', \Route::currentRouteName())->first();
        if (!$return) {
            $route = preg_replace('/\.[a-zA-Z0-9\_\-]+$/', '', \Route::currentRouteName());
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
     * @return \Illuminate\Database\Eloquent\Model|string|null
     */
    public static function model()
    {
        $return = null;

        $menu = gets()->lte->menu->now;

        if ($menu && isset($menu['model'])) {

            $return = $menu['model'];

            if (is_string($return) && class_exists($return)) {

                /** @var Model $return */
                $return = new $return;

                if ($return instanceof Model) {

                    $roue_param = \Route::current()->parameter($menu['model.param']);

                    //dd($menu['model.param']);

                    if ($roue_param) {

                        if ($find = $return->where($return->getRouteKeyName(), $roue_param)->first()) {

                            $return = $find;
                        }
                    }
                }
            }
        }

        return $return;
    }

    /**
     * @return \Illuminate\Support\Collection|array
     */
    public static function now_parents()
    {
        return gets()->lte->menu->now ? collect(static::get_parents(gets()->lte->menu->now)) : collect([]);
    }

    /**
     * @param array $subject
     * @param array $result
     * @return array
     */
    protected static function get_parents(array $subject, $result = [])
    {
        $result[$subject['id']] = $subject;

        if ($subject['parent_id']) {

            $parent = gets()->lte->menu->nested_collect->where('active', true)->where('id', $subject['parent_id'])->first();

            if ($parent) {

                return static::get_parents($parent, $result);
            }

            else {

                return $result;
            }
        }

        return $result;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public static function nested_collect()
    {
        return collect(gets()->lte->menu->nested);
    }

    /**
     * @param  bool  $__route_items_
     * @param  int  $__route_parent_id_
     * @param  string  $__route_name_
     * @param  array|null  $__parent
     * @return array
     */
    public static function nested($__route_items_ = false, int $__route_parent_id_ = 0, $__route_name_ = 'lte', array $__parent = null)
    {
        if ($__route_items_ === false) { $__route_items_ = \Navigate::getMaked(); }

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
                'parent_id' => $__route_parent_id_
            ];

            if ($__parent && isset($__parent['roles'])) {

                $item['roles'] = $__parent['roles'];
            }

            if (!isset($item['route'])) {

                $item['route'] = false;
            }

            else if ($__route_name_) {

                if (str_replace(['{','?','}'], '', $item['route']) !== $item['route']) {

                    $item['route'] = $__route_name_;
                }

                else {

                    $item['route'] = $__route_name_ . '.' . (isset($item['resource']['name']) ? str_replace('/', '.', $item['resource']['name']) : $item['route']);
                }
            }

            $item['target'] = false;

            if (!isset($item['link'])) {

                $item['link'] = false;
            }

            else if (preg_match('/^http/', $item['link'])) {

                $item['target'] = true;
            }

            $item['current.type'] = null;

            if (isset($item['model']) && !isset($item['model.param'])) {

                $item['model.param'] = \Str::singular(\Str::snake(class_basename($item['model'])));
            }

            if ($item['route'] && \Route::has($item['route'])) {

                $item['link'] = route($item['route'], $item['route_params'] ?? []);
            }

            else if (isset($item['resource']) && \Route::has($item['route'] . '.index')) {

                $item['current.type'] = str_replace($item['route'] . '.', '', \Route::currentRouteName());

                $item['link'] = route($item['route'] . '.index', $item['route_params'] ?? []);

                $item['link.show'] = function ($params) use ($item) {
                    if (!is_array($params) && isset($item['model.param'])) { $params = [$item['model.param'] => $params]; }
                    return route($item['route'] . '.show', array_merge(($item['route_params'] ?? []), $params));
                };
                $item['link.update'] = function ($params) use ($item) {
                    if (!is_array($params) && isset($item['model.param'])) { $params = [$item['model.param'] => $params]; }
                    return route($item['route'] . '.update', array_merge(($item['route_params'] ?? []), $params));
                };
                $item['link.destroy'] = function ($params) use ($item) {
                    if (!is_array($params) && isset($item['model.param'])) { $params = [$item['model.param'] => $params]; }
                    return route($item['route'] . '.destroy', array_merge(($item['route_params'] ?? []), $params));
                };
                $item['link.edit'] = function ($params) use ($item) {
                    if (!is_array($params) && isset($item['model.param'])) { $params = [$item['model.param'] => $params]; }
                    return route($item['route'] . '.edit', array_merge(($item['route_params'] ?? []), $params));
                };
                $item['link.store'] = route($item['route'] . '.store', $item['route_params'] ?? []);
                $item['link.create'] = route($item['route'] . '.create', $item['route_params'] ?? []);
            }

            if (!isset($item['selected'])) {

                $item['selected'] = false;
            }

            if (!$item['selected'] && $item['route']) {

                $current_route = \Route::currentRouteName();

                $item['selected'] = \Str::is($item['route'].'*', $current_route);
            }

            else if (!$item['selected'] && $item['link'] && !$item['target']) {
                $link = trim($item['link'], '/');
                $link = ltrim($link, \App::getLocale());
                $link = trim($link, '/');
                $path = ltrim(request()->decodedPath().'/', \App::getLocale());
                $path = trim($path, '/');

                $item['link'] = "/".\App::getLocale().'/'.$link;

                $item['selected'] = \Str::is($link.'*', $path);
            }

            if (!isset($item['active'])) {

                $item['active'] = isset($item['title']);
            }

            else if ($item['active'] && !isset($item['title'])) {

                $item['title'] = false;
            }

            if (isset($item['link']) && $item['link'] && isset($item['active']) && $item['active']) {

                $item['active'] = LtePermission::checkUrl($item['link']);
            }

            if (isset($item['func']) && $item['func'] && $item['active']) {

                $item['active'] = lte_user()->func()->has($item['func']);
            }

            $result = array_merge($add, $item);

            $return[] = $result;

            $last = array_key_last($return);

            static::$nested_counter++;

            if ($childs) {

                $chl = static::nested($childs, $id, $item['route'] ?? 'lte', $result);

                $return[$last]['active'] = !!collect($chl)->where('active', true)->count();

                $return = array_merge($return, $chl);
            }
        }

        return $return;
    }


    public static function collapse($item)
    {

    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function default()
    {
        return collect([]);
    }
}
