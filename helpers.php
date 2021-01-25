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
    function admin_asset (string $path = '', $secure = null) {

        return asset(admin_asset_url_path($path), $secure);
    }
}

if ( ! function_exists('vendor_asset') ) {

    /**
     * @param string|null $path
     * @param  null  $secure
     * @return string
     */
    function vendor_asset (string $path = '', $secure = null) {

        return asset(vendor_asset_url_path($path), $secure);
    }
}

if ( ! function_exists('admin_path_asset') ) {

    /**
     * @param  string  $path
     * @return string
     */
    function admin_path_asset (string $path = '') {

        return public_path(admin_asset_url_path($path));
    }
}

if ( ! function_exists('admin_asset_url_path') ) {

    /**
     * @param  string  $path
     * @return string
     */
    function admin_asset_url_path (string $path = '') {

        return "vendor/admin/" . trim($path, "/");
    }
}

if ( ! function_exists('vendor_asset_url_path') ) {

    /**
     * @param  string  $path
     * @return string
     */
    function vendor_asset_url_path (string $path = '') {

        return "vendor/" . trim($path, "/");
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

        return trim(config('admin.route.prefix'), '/') . $uri;
    }
}