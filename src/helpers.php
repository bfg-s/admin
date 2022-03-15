<?php

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use LteAdmin\Models\LtePermission;
use LteAdmin\Models\LteUser;
use LteAdmin\Page;
use LteAdmin\Repositories\AdminRepository;

if (!function_exists('lte_log')) {
    /**
     * @param  string  $title
     * @param  string|null  $detail
     * @param  string|null  $icon
     * @return string
     */
    function lte_log(string $title, ?string $detail = null, string $icon = null)
    {
        $params = [];
        $params['icon'] = $icon ?: (admin_repo()->now ? admin_repo()->now['icon'] : $icon);
        $params['title'] = $title;
        $params['detail'] = $detail;
        $params['ip'] = request()->ip();
        $params['url'] = url()->current();
        $params['route'] = Route::currentRouteName();
        $params['method'] = request()->method();
        $params['web_id'] = Auth::check() ? Auth::id() : null;
        $params['session_id'] = session()->getId();
        $params['user_agent'] = request()->userAgent();

        return admin() ? admin()->logs()->create($params) : false;
    }

    function lte_log_warning(string $title, ?string $detail = null, string $icon = null)
    {
        return lte_log($title, $detail, ($icon ?: 'fas fa-lightbulb').' bg-warning');
    }

    function lte_log_primary(string $title, ?string $detail = null, string $icon = null)
    {
        return lte_log($title, $detail, ($icon ?: 'fas fa-lightbulb').' bg-primary');
    }

    function lte_log_secondary(string $title, ?string $detail = null, string $icon = null)
    {
        return lte_log($title, $detail, ($icon ?: 'fas fa-lightbulb').' bg-secondary');
    }

    function lte_log_success(string $title, ?string $detail = null, string $icon = null)
    {
        return lte_log($title, $detail, ($icon ?: 'fas fa-lightbulb').' bg-success');
    }

    function lte_log_info(string $title, ?string $detail = null, string $icon = null)
    {
        return lte_log($title, $detail, ($icon ?: 'fas fa-lightbulb').' bg-info');
    }

    function lte_log_danger(string $title, ?string $detail = null, string $icon = null)
    {
        return lte_log($title, $detail, ($icon ?: 'fas fa-lightbulb').' bg-danger');
    }

    function lte_log_dark(string $title, ?string $detail = null, string $icon = null)
    {
        return lte_log($title, $detail, ($icon ?: 'fas fa-lightbulb').' bg-dark');
    }
}

if (!function_exists('lte_relative_path')) {
    /**
     * @param  string  $path
     * @return string
     */
    function lte_relative_path(string $path = '')
    {
        return '/'.trim('/'.trim(str_replace(base_path(), '', config('lte.paths.app')), '/')
                .'/'.trim($path, '/'), '/');
    }
}
if (!function_exists('lte_app_namespace')) {
    /**
     * @param  string  $path
     * @return string
     */
    function lte_app_namespace(string $path = '')
    {
        return trim('\\'.trim(config('lte.app_namespace'), '\\')
            .'\\'.trim($path, '\\'), '\\');
    }
}

if (!function_exists('lte_related_methods')) {
    /**
     * @param  string  $method
     * @return string[]
     */
    function lte_related_methods(string $method)
    {
        if ($method == 'store') {
            $methods = [$method, 'create', 'access'];
        } elseif ($method == 'update') {
            $methods = [$method, 'edit', 'access'];
        } elseif ($method == 'create') {
            $methods = [$method, 'store', 'access'];
        } elseif ($method == 'edit') {
            $methods = [$method, 'update', 'access'];
        } elseif ($method == 'destroy') {
            $methods = [$method, 'delete', 'access'];
        } elseif ($method == 'delete') {
            $methods = [$method, 'destroy', 'access'];
        } else {
            $methods = [$method, 'access'];
        }

        return $methods;
    }
}

if (!function_exists('lte_controller_model')) {
    /**
     * @return string
     */
    function lte_controller_model()
    {
        $class = Str::parseCallback(Route::currentRouteAction())[0];

        if (property_exists($class, 'model')) {
            return $class::$model;
        }

        return '';
    }
}

if (!function_exists('lte_app_path')) {
    /**
     * @param  string  $path
     * @return string
     */
    function lte_app_path(string $path = '')
    {
        return rtrim(config('lte.paths.app').'/'.trim($path, '/'), '/');
    }
}

if (!function_exists('lte_uri')) {
    /**
     * @param  string  $uri
     * @return string
     */
    function lte_uri(string $uri = '')
    {
        if (!empty($uri)) {
            $uri = '/'.trim($uri, '/');
        }

        return (config('layout.lang_mode') ? '/'.Layout::nowLang() : '').'/'.trim(config('lte.route.prefix'), '/').$uri;
    }
}

if (!function_exists('lte_asset')) {
    /**
     * @param  string  $link
     * @return string
     */
    function lte_asset(string $link = null)
    {
        if ($link) {
            return asset('lte-admin/'.trim($link, '/'));
        }

        return asset('lte-admin');
    }
}

if (!function_exists('lte_user')) {
    /**
     * @return LteUser|Admin
     */
    function lte_user()
    {
        return LteAdmin::user() ?? new LteUser();
    }
}

if (!function_exists('admin')) {
    /**
     * @return LteUser|Admin
     */
    function admin()
    {
        return LteAdmin::user() ?? new LteUser();
    }
}

if (!function_exists('versionString')) {
    /**
     * @param $version
     * @param  string  $delimiter
     * @return string
     */
    function versionString($version, string $delimiter = '.')
    {
        $version = explode($delimiter, $version);

        $total = count($version);

        foreach ($version as $key => $item) {
            if ($key === ($total - 1)) {
                $version[$key] = "<small>{$item}</small>";
            }
        }

        return implode($delimiter, $version);
    }
}

if (!function_exists('resource_name')) {
    /**
     * @param  string  $append
     * @return string
     */
    function resource_name(string $append = '')
    {
        return preg_replace(
                '/(.*)\.(store|index|create|show|update|destroy|edit)$/',
                '$1',
                Route::currentRouteName()
            ).$append;
    }
}

if (!function_exists('makeUrlWithParams')) {
    /**
     * @param  string  $url
     * @param  array  $params
     * @return string
     */
    function makeUrlWithParams(string $url, array $params)
    {
        $params = http_build_query($params);
        $d = !str_contains($url, '?') ? '?' : '&';

        return $url.$d.$params;
    }
}

if (!function_exists('urlWithGet')) {
    /**
     * @param  array  $params
     * @param  array  $unset
     * @return string
     */
    function urlWithGet(array $params = [], array $unset = [])
    {
        $url = explode('?', url()->current())[0];

        $params = Arr::dot(array_merge(request()->query(), $params));
        foreach ($unset as $k => $item) {
            if (isset($params[$item])) {
                unset($params[$item], $unset[$k]);
            }
        }
        $params = array_dots_uncollapse($params);
        foreach ($unset as $item) {
            if (isset($params[$item])) {
                unset($params[$item]);
            }
        }

        return $url.(count($params) ? '?'.http_build_query($params) : '');
    }
}

if (!function_exists('lte_model_type')) {
    /**
     * @param  string|null  $type
     * @return bool|string|null
     */
    function lte_model_type(string $type = null)
    {
        $menu_type = admin_repo()->type;

        if ($type) {
            return $menu_type === $type;
        }

        return $menu_type;
    }
}

if (!function_exists('lte_model')) {
    /**
     * @param  string|null  $path
     * @return Model|mixed|string|null
     */
    function lte_model(string $path = null)
    {
        $model = admin_repo()->modelNow;

        if ($model && $model->exists) {
            if ($path) {
                return multi_dot_call($model, $path);
            }

            return $model;
        }

        return null;
    }
}

if (!function_exists('lte_now')) {
    /**
     * @return array|array|null
     */
    function lte_now()
    {
        return admin_repo()->now;
    }
}

if (!function_exists('lte_page')) {
    /**
     * @return array|array|null
     */
    function lte_page()
    {
        return app(Page::class);
    }
}

if (!function_exists('array_callable_results')) {
    /**
     * @param  array  $array
     * @param  mixed  ...$callable_params
     * @return array
     */
    function array_callable_results(array $array, ...$callable_params)
    {
        foreach ($array as $key => $item) {
            if ($item instanceof Closure) {
                $array[$key] = $item(...$callable_params);
            }
        }

        return $array;
    }
}

if (!function_exists('check_referer')) {
    /**
     * @param  string  $method
     * @param  string|null  $url
     * @return bool
     */
    function check_referer(string $method = 'GET', string $url = null)
    {
        $referer = request()->headers->get('referer');

        $result = false;

        if ($url || $referer) {
            $result = LtePermission::checkUrl($url ?: $referer, $method);
        }

        if (!$result) {
            respond()->toast_error([__('lte.access_denied'), __('lte.error')]);
        }

        return $result;
    }
}

if (!function_exists('admin_repo')) {
    function admin_repo()
    {
        return app(AdminRepository::class);
    }
}
