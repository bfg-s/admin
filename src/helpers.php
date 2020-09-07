<?php

if (!function_exists('lte_relative_path')) {

    /**
     * @param  string  $path
     * @return string
     */
    function lte_relative_path (string $path = "") {

        return trim("/" . trim(str_replace(base_path(), '', config('lte.paths.app')), "/")
            . "/" . trim($path, "/"), "/");
    }
}
if (!function_exists('lte_app_namespace')) {

    /**
     * @param  string  $path
     * @return string
     */
    function lte_app_namespace (string $path = "") {

        return trim("\\" . trim(config('lte.app_namespace'), "\\")
            . "\\" . trim($path, "\\"), "\\");
    }
}

if (!function_exists('lte_related_methods')) {

    /**
     * @param  string  $method
     * @return string[]
     */
    function lte_related_methods (string $method) {
        if ($method == 'store') { $methods = [$method, 'create', 'access']; }
        else if ($method == 'update') { $methods = [$method, 'edit', 'access']; }
        else if ($method == 'create') { $methods = [$method, 'store', 'access']; }
        else if ($method == 'edit') { $methods = [$method, 'update', 'access']; }
        else if ($method == 'destroy') { $methods = [$method, 'delete', 'access']; }
        else if ($method == 'delete') { $methods = [$method, 'destroy', 'access']; }
        else { $methods = [$method, 'access']; }

        return $methods;
    }
}

if (!function_exists('lte_class_can')) {

    /**
     * @param string|array $class
     * @param  string  $method
     * @return bool
     */
    function lte_class_can ($class, string $method) {

        $func = $func = gets()->lte->functions->list
            ->whereIn('class', (array)$class)
            ->whereIn('slug', lte_related_methods($method));

        /** @var \Lar\LteAdmin\Models\LteFunction $item */
        foreach ($func as $item) {
            if (!lte_user()->hasRoles($item->roles->pluck('slug')->toArray())) {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('lte_controller_can')) {
    /**
     * @param  string|null  $check_method
     * @param  string|null  $check_class
     * @return string
     */
    function lte_controller_can (string $check_method = null, string $check_class = null) {

        list($class, $method) = \Str::parseCallback(\Route::currentRouteAction());

        if ($check_method) { $method = $check_method; }
        if ($check_class) { $class = $check_class; }
        if (!$class) { return true; }

        $class = trim($class, '\\');
        $ability = "{$class}@{$method}";

        return Gate::has($ability) ? Gate::forUser(admin())->allows($ability) : true;
    }
}

if (!function_exists('lte_app_path')) {

    /**
     * @param string $path
     * @return string
     */
    function lte_app_path (string $path = '') {

        return trim(trim(config('lte.paths.app'), '/') . '/' . trim($path, '/'), '/');
    }
}

if ( ! function_exists('lte_uri') ) {

    /**
     * @param string $uri
     * @return string
     */
    function lte_uri (string $uri = '') {

        if (!empty($uri)) {

            $uri = "/" . trim($uri, '/');
        }

        return (config('layout.lang_mode') ? '/' . Layout::nowLang() : '') . '/' . trim(config('lte.route.prefix'), '/') . $uri;
    }
}

if ( ! function_exists('lte_asset') ) {

    /**
     * @param string $link
     * @return string
     */
    function lte_asset (string $link = null) {

        if ($link) {

            return asset('lte-admin/' . trim($link, '/'));
        }

        return asset('lte-admin');
    }
}

if ( ! function_exists('lte_user') ) {

    /**
     * @return \Lar\LteAdmin\Models\LteUser|\App\Models\Admin
     */
    function lte_user () {

        return LteAdmin::user() ?? new \Lar\LteAdmin\Models\LteUser();
    }
}

if ( ! function_exists('admin') ) {

    /**
     * @return \Lar\LteAdmin\Models\LteUser|\App\Models\Admin
     */
    function admin () {

        return LteAdmin::user() ?? new \Lar\LteAdmin\Models\LteUser();
    }
}

if ( ! function_exists('lte_func') ) {

    /**
     * @return \Lar\LteAdmin\Core\CheckUserFunction
     */
    function lte_func () {

        return lte_user()->func();
    }
}

if ( ! function_exists('versionString') ) {

    /**
     * @param $version
     * @param string $delimiter
     * @return string
     */
    function versionString($version, string $delimiter = '.')
    {
        $version = explode($delimiter, $version);

        $total = count($version);

        foreach ($version as $key => $item) {

            if ($key === ($total-1)) {

                $version[$key] = "<small>{$item}</small>";
            }
        }

        return implode($delimiter, $version);
    }
}


if ( ! function_exists('resource_name') ) {

    /**
     * @param string $append
     * @return string
     */
    function resource_name(string $append = "")
    {
        return preg_replace('/(.*)\.(store|index|create|show|update|destroy|edit)$/', '$1', Route::currentRouteName()) . $append;
    }
}

if ( ! function_exists('makeUrlWithParams') ) {

    /**
     * @param  string  $url
     * @param  array  $params
     * @return string
     */
    function makeUrlWithParams(string $url, array $params)
    {
        $params = http_build_query($params);
        $d = strpos($url, '?') === false ? "?" : "&";
        return $url.$d.$params;
    }
}

if ( ! function_exists('urlWithGet') ) {

    /**
     * @param  array  $params
     * @param  array  $unset
     * @return string
     */
    function urlWithGet(array $params = [], array $unset = [])
    {
        $url = explode('?', url()->current())[0];

        $params = array_merge(request()->query(), $params);

        unset($params['_pjax']);

        foreach ($unset as $item) {
            if (isset($params[$item])) { unset($params[$item]); }
        }

        return $url . (count($params) ? '?' . http_build_query($params) : '');
    }
}


if ( ! function_exists('lte_model_type') ) {

    /**
     * @param  string|null  $type
     * @return bool|\Lar\LteAdmin\Getters\Menu|string|null
     */
    function lte_model_type(string $type = null) {

        $menu_type = gets()->lte->menu->type;

        if ($type) {

            return $menu_type === $type;
        }

        return $menu_type;
    }
}

if ( ! function_exists('lte_model') ) {

    /**
     * @param  string|null  $path
     * @return \Illuminate\Database\Eloquent\Model|\Lar\LteAdmin\Getters\Menu|mixed|string|null
     */
    function lte_model(string $path = null)
    {
        $model = gets()->lte->menu->model;

        if ($model && $model->exists) {

            if ($path) {

                return multi_dot_call($model, $path);
            }

            return $model;
        }

        return null;
    }
}

if ( ! function_exists('lte_now') ) {

    /**
     * @return array|\Lar\LteAdmin\Getters\Menu|array|null
     */
    function lte_now()
    {
        return gets()->lte->menu->now;
    }
}