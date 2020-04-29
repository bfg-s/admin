<?php

if (!function_exists('lte_app_path')) {

    /**
     * @param string $path
     * @return string
     */
    function lte_app_path (string $path = '') {

        return config('lte.paths.app') . '/' . trim($path, '/');
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
     * @return \Lar\LteAdmin\Models\LteUser
     */
    function lte_user () {

        return LteAdmin::user();
    }
}

if ( ! function_exists('admin') ) {

    /**
     * @return \Lar\LteAdmin\Models\LteUser
     */
    function admin () {

        return LteAdmin::user();
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
     * @param array $params
     * @return string
     */
    function urlWithGet(array $params = [])
    {
        $url = explode('?', url()->current())[0];

        $params = array_merge(request()->query(), $params);

        unset($params['_pjax']);

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

if (!function_exists('remove_dir')) {

    /**
     * @param $dirPath
     */
    function remove_dir($dirPath) {

        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') { $dirPath .= '/'; }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) { if (is_dir($file)) { remove_dir($file); } else { unlink($file); } } try { rmdir($dirPath); } catch (Exception $e) {}
    }
}