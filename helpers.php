<?php

if ( ! function_exists('admin') ) {

    /**
     * @return \Admin\Models\AdminUser
     */
    function admin () {

        return \Admin::user();
    }
}

if ( ! function_exists('admin_asset') ) {

    /**
     * @param string|null $path
     * @param  null  $secure
     * @return string
     */
    function admin_asset (string $path = null, $secure = null) {

        return asset(admin_url_path($path), $secure);
    }
}

if ( ! function_exists('admin_url_path') ) {

    /**
     * @param  string|null  $path
     * @return string
     */
    function admin_url_path (string $path = null) {

        return "vendor/admin" . ($path ? "/" . trim($path, "/") : "");
    }
}

if (!function_exists('admin_app_path')) {

    /**
     * @param string $path
     * @return string
     */
    function admin_app_path (string $path = '') {

        return "/" . trim(trim(config('admin.paths.app'), '/') . '/' . trim($path, '/'), '/');
    }
}

if (!function_exists('admin_app_namespace')) {

    /**
     * @param  string  $path
     * @return string
     */
    function admin_app_namespace (string $path = "") {

        return trim("\\" . trim(config('admin.namespace'), "\\")
            . "\\" . trim($path, "\\"), "\\");
    }
}

if ( ! function_exists('admin_uri') ) {

    /**
     * @param string $uri
     * @return string
     */
    function admin_uri (string $uri = '') {

        if (!empty($uri)) {

            $uri = "/" . trim($uri, '/');
        }

        return trim(config('lte.route.prefix'), '/') . $uri;
    }
}