<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Admin\Core\MenuItem;
use Admin\Models\AdminPermission;
use Admin\Models\AdminUser;
use Admin\Page;
use Admin\Repositories\AdminRepository;

if (!function_exists('admin_log')) {
    /**
     * @param  string  $title
     * @param  string|null  $detail
     * @param  string|null  $icon
     * @return string
     */
    function admin_log(string $title, ?string $detail = null, string $icon = null): bool|string
    {
        $params = [];
        $params['icon'] = $icon ?: (admin_repo()->now ? admin_repo()->now->getIcon() : $icon);
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

    function admin_log_warning(string $title, ?string $detail = null, string $icon = null): bool|string
    {
        return admin_log($title, $detail, ($icon ?: 'fas fa-lightbulb').' bg-warning');
    }

    function admin_log_primary(string $title, ?string $detail = null, string $icon = null): bool|string
    {
        return admin_log($title, $detail, ($icon ?: 'fas fa-lightbulb').' bg-primary');
    }

    function admin_log_secondary(string $title, ?string $detail = null, string $icon = null): bool|string
    {
        return admin_log($title, $detail, ($icon ?: 'fas fa-lightbulb').' bg-secondary');
    }

    function admin_log_success(string $title, ?string $detail = null, string $icon = null): bool|string
    {
        return admin_log($title, $detail, ($icon ?: 'fas fa-lightbulb').' bg-success');
    }

    function admin_log_info(string $title, ?string $detail = null, string $icon = null): bool|string
    {
        return admin_log($title, $detail, ($icon ?: 'fas fa-lightbulb').' bg-info');
    }

    function admin_log_danger(string $title, ?string $detail = null, string $icon = null): bool|string
    {
        return admin_log($title, $detail, ($icon ?: 'fas fa-lightbulb').' bg-danger');
    }

    function admin_log_dark(string $title, ?string $detail = null, string $icon = null): bool|string
    {
        return admin_log($title, $detail, ($icon ?: 'fas fa-lightbulb').' bg-dark');
    }
}

if (!function_exists('admin_relative_path')) {
    /**
     * @param  string  $path
     * @return string
     */
    function admin_relative_path(string $path = ''): string
    {
        return '/'.trim('/'.trim(str_replace(base_path(), '', config('admin.paths.app')), '/')
                .'/'.trim($path, '/'), '/');
    }
}
if (!function_exists('admin_app_namespace')) {
    /**
     * @param  string  $path
     * @return string
     */
    function admin_app_namespace(string $path = ''): string
    {
        return trim('\\'.trim(config('admin.app_namespace'), '\\')
            .'\\'.trim($path, '\\'), '\\');
    }
}

if (!function_exists('admin_related_methods')) {
    /**
     * @param  string  $method
     * @return string[]
     */
    function admin_related_methods(string $method): array
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

if (!function_exists('admin_controller_model')) {
    /**
     * @return string
     */
    function admin_controller_model(): string
    {
        $class = Str::parseCallback(Route::currentRouteAction())[0];

        if (property_exists($class, 'model')) {
            return $class::$model;
        }

        return '';
    }
}

if (!function_exists('admin_app_path')) {
    /**
     * @param  string  $path
     * @return string
     */
    function admin_app_path(string $path = ''): string
    {
        return rtrim(config('admin.paths.app').'/'.trim($path, '/'), '/');
    }
}

if (!function_exists('admin_uri')) {
    /**
     * @param  string  $uri
     * @return string
     */
    function admin_uri(string $uri = ''): string
    {
        if (!empty($uri)) {
            $uri = '/'.trim($uri, '/');
        }

        return (config('layout.lang_mode') ? '/'.Layout::nowLang() : '').'/'.trim(config('admin.route.prefix'), '/').$uri;
    }
}

if (!function_exists('admin_asset')) {
    /**
     * @param  string|null  $link
     * @return string
     */
    function admin_asset(string $link = null): string
    {
        if ($link) {
            return asset('admin/'.trim($link, '/'));
        }

        return asset('admin');
    }
}

if (!function_exists('admin_user')) {
    /**
     * @return AdminUser
     */
    function admin_user(): AdminUser
    {
        return Admin::user() ?? new AdminUser();
    }
}

if (!function_exists('admin')) {
    /**
     * @return AdminUser
     */
    function admin(): AdminUser
    {
        return Admin::user() ?? new AdminUser();
    }
}

if (!function_exists('versionString')) {
    /**
     * @param $version
     * @param  string  $delimiter
     * @return string
     */
    function versionString($version, string $delimiter = '.'): string
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

if (!function_exists('admin_model_type')) {
    /**
     * @param  string|null  $type
     * @return bool|string|null
     */
    function admin_model_type(string $type = null)
    {
        $menu_type = admin_repo()->type;

        if ($type) {
            return $menu_type === $type;
        }

        return $menu_type;
    }
}

if (!function_exists('admin_model')) {
    /**
     * @param  string|null  $path
     * @return Model|mixed|string|null
     */
    function admin_model(string $path = null)
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

if (!function_exists('admin_now')) {
    /**
     * @return MenuItem|null
     */
    function admin_now()
    {
        return admin_repo()->now;
    }
}

if (!function_exists('admin_page')) {
    /**
     * @return array|array|null
     */
    function admin_page()
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
            $result = AdminPermission::checkUrl($url ?: $referer, $method);
        }

        if (!$result) {
            respond()->toast_error([__('admin.access_denied'), __('admin.error')]);
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

if (!function_exists('back_validate')) {
    /**
     * Validator with back response.
     *
     * @param  array  $subject
     * @param  array  $rules
     * @param  array  $messages
     * @return bool|RedirectResponse|Respond
     */
    function back_validate(array $subject, array $rules, array $messages = [])
    {
        $rules_new = [];

        if (request()->has('__only_has')) {
            foreach ($subject as $key => $item) {
                if (isset($rules[$key])) {
                    $rules_new[$key] = $rules[$key];
                }
            }
            $rules = $rules_new;
        }

        if ($result = quick_validate($subject, $rules, $messages)) {
            if (request()->ajax() && !request()->pjax()) {
                foreach ($result->errors()->messages() as $key => $message) {
                    foreach ($message as $item) {
                        Respond::glob()->toast_error($item);
                    }
                }

                if (request()->ajax() && !request()->pjax()) {
                    Respond::glob()->reload();
                }

                return Respond::glob();
            }

            return back()->withInput()->withErrors($result);
        }

        return false;
    }
}

if (!function_exists('quick_validate')) {
    /**
     * Quick validate collection.
     *
     * @param $subject
     * @param  array  $rules
     * @param  array  $messages
     * @return bool|\Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    function quick_validate(array $subject, array $rules, array $messages = [])
    {
        $result = \Illuminate\Support\Facades\Validator::make($subject, $rules, $messages);

        if ($result->fails()) {
            return $result;
        }

        return false;
    }
}

if (!function_exists('respond_validate')) {
    /**
     * Quick validate collection.
     *
     * @param $subject
     * @param  array  $rules
     * @param  array  $messages
     * @return Collection|bool
     */
    function respond_validate($subject, array $rules, array $messages = [])
    {
        return collect($subject)->line_validate($rules, $messages);
    }
}

if (!function_exists('is_image')) {
    /**
     * Is Image.
     *
     * @param $path
     * @return bool
     */
    function is_image($path)
    {
        try {
            return (bool) exif_imagetype($path);
        } catch (Exception $exception) {
        }

        return false;
    }
}

if (!function_exists('butty_date')) {
    function butty_date($time)
    {
        if (! $time) return $time;

        $timestamp = strtotime($time);
        $published = date('d.m.Y', $timestamp);

        if ($published === date('d.m.Y')) {
            return trans('admin.date.today_short', ['time' => date('H:i', $timestamp)]);
        } elseif ($published === date('d.m.Y', strtotime('-1 day'))) {
            return trans('admin.date.yesterday_short', ['time' => date('H:i', $timestamp)]);
        } else {
            $formatted = trans('admin.date.later_short', [
                'date' => date('d F'.(date('Y', $timestamp) === date('Y') ? null : ' Y'), $timestamp),
            ]);

            return strtr($formatted, trans('admin.date.month_declensions'));
        }
    }
}

if (!function_exists('butty_date_time')) {
    function butty_date_time($time)
    {
        if (! $time) return $time;

        $timestamp = strtotime($time);

        $published = date('d.m.Y', $timestamp);

        if ($published === date('d.m.Y')) {
            return trans('admin.date.today', ['time' => date('H:i', $timestamp)]);
        } elseif ($published === date('d.m.Y', strtotime('-1 day'))) {
            return trans('admin.date.yesterday', ['time' => date('H:i', $timestamp)]);
        } else {
            $formatted = trans('admin.date.later', [
                'time' => date('H:i', $timestamp),
                'date' => date('d F'.(date('Y', $timestamp) === date('Y') ? null : ' Y'), $timestamp),
            ]);

            return strtr($formatted, trans('admin.date.month_declensions'));
        }
    }
}

if (!function_exists('array_dots_uncollapse')) {
    /**
     * Expand an array folded into a dot array.
     *
     * @param  array  $array
     * @return array
     */
    function array_dots_uncollapse(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            Arr::set($result, $key, $value);
        }

        return $result;
    }
}
