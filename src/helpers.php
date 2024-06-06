<?php

use Admin\Core\MenuItem;
use Admin\Facades\Admin;
use Admin\Models\AdminPermission;
use Admin\Models\AdminUser;
use Admin\Page;
use Admin\Repositories\AdminRepository;
use Admin\Respond;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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
        if ($action = Route::currentRouteAction()) {
            $class = Str::parseCallback($action)[0];

            if (method_exists($class, 'getModel')) {
                return call_user_func([$class, 'getModel']);
            } else if (property_exists($class, 'model')) {
                return $class::$model;
            }
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

        return (config('layout.lang_mode') ? '/'.Admin::nowLang() : '').'/'.trim(config('admin.route.prefix'),
                '/').$uri;
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

if (!function_exists('admin_version_string')) {
    /**
     * @param $version
     * @param  string  $delimiter
     * @return string
     */
    function admin_version_string($version, string $delimiter = '.'): string
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
    function resource_name(string $append = ''): string
    {
        return preg_replace(
                '/(.*)\.(store|index|create|show|update|destroy|edit)$/',
                '$1',
                Route::currentRouteName() ?: ''
            ).$append;
    }
}

if (!function_exists('admin_make_url_with_params')) {
    /**
     * @param  string  $url
     * @param  array  $params
     * @return string
     */
    function admin_make_url_with_params(string $url, array $params = []): string
    {
        $params = http_build_query($params);
        $d = !str_contains($url, '?') ? '?' : '&';

        return $url.$d.$params;
    }
}

if (!function_exists('admin_url_with_get')) {
    /**
     * @param  array  $params
     * @param  array  $unset
     * @return string
     */
    function admin_url_with_get(array $params = [], array $unset = []): string
    {
        $modal = request()->input('_modal');
        $realtime = request()->input('_realtime');
        $currentUrl = $modal || $realtime
            ? \Illuminate\Support\Facades\Request::server('HTTP_REFERER')
            : url()->current();

        $url = explode('?', $currentUrl)[0];

        $params = \Illuminate\Support\Arr::dot(array_merge(request()->query(), $params));
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
    function admin_model_type(string $type = null): bool|string|null
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
    function admin_model(string $path = null): mixed
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
    function admin_now(): ?MenuItem
    {
        return admin_repo()->now;
    }
}

if (!function_exists('admin_page')) {
    /**
     * @return Page
     */
    function admin_page(): Page
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
    function array_callable_results(array $array, ...$callable_params): array
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
    function check_referer(string $method = 'GET', string $url = null): bool
    {
        $referer = request()->headers->get('referer');

        $result = false;

        if ($url || $referer) {
            $result = AdminPermission::checkUrl($url ?: $referer, $method);
        }

        if (!$result) {
            Respond::glob()->toast_error([__('admin.access_denied'), __('admin.error')]);
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

if (!function_exists('beautiful_date')) {
    function beautiful_date($time)
    {
        if (!$time) {
            return $time;
        }

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

if (!function_exists('beautiful_date_time')) {
    function beautiful_date_time($time)
    {
        if (!$time) {
            return $time;
        }

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

if (!function_exists('array_is_list')) {
    /**
     * Check if array is a list (no assoc)
     * @param  array  $arr
     * @return bool
     */
    function array_is_list(array $arr): bool
    {
        if ($arr === []) {
            return true;
        }
        return array_keys($arr) === range(0, count($arr) - 1);
    }
}

if (!function_exists('admin_template')) {
    /**
     * @param  string  $template
     * @return string
     */
    function admin_template(string $template): string
    {
        return Admin::getTheme()->template($template);
    }
}

if (!function_exists('admin_view')) {
    /**
     * @param $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return View
     */
    function admin_view($view = null, array $data = [], array $mergeData = []): View
    {
        return view(admin_template($view), $data, $mergeData);
    }
}

if (!function_exists('is_embedded_call')) {
    /**
     * @param  mixed  $subject
     * @return bool
     */
    function is_embedded_call(mixed $subject): bool
    {
        return is_string($subject) ? class_exists($subject) : is_callable($subject);
    }
}

if (!function_exists('body_namespace_element')) {
    /**
     * Get only namespace body.
     *
     * @param  string  $namespace
     * @param  int  $level
     * @param  string  $delimiter
     * @return string
     */
    function body_namespace_element(string $namespace, int $level = 1, string $delimiter = '\\'): string
    {
        return Bfg\Entity\Core\Entities\NamespaceEntity::bodySegment($namespace, $level);
    }
}

if (!function_exists('remake_lang_url')) {
    /**
     * @param  string  $lang
     * @param  string|null  $url
     * @return string
     */
    function remake_lang_url(string $lang, string $url = null): string
    {
        if (!$url) {
            $url = url()->current();
        }

        return preg_replace("/(.*:\/\/.*\/)(".App::getLocale().')(.*)/', "$1{$lang}$3", $url);
    }
}

if (!function_exists('getBrowserDetails')) {
    /**
     * @param $u_agent
     * @return array
     */
    function getBrowserDetails($u_agent): array
    {
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/OPR/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Chrome/i', $u_agent) && !preg_match('/Edge/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent) && !preg_match('/Edge/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        } elseif (preg_match('/Edge/i', $u_agent)) {
            $bname = 'Edge';
            $ub = "Edge";
        } elseif (preg_match('/Trident/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } else {
            $ub = "Unknown";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>'.join('|', $known).
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1] ?? '0.0.1';
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {
            $version = "?";
        }

        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern
        );
    }
}

if (! function_exists('admin_show_model_field')) {
    /**
     * Function for generating a string data from input any data.
     *
     * @param  mixed  $data
     * @param  mixed  $model
     * @param  mixed  ...$args
     * @return mixed
     */
    function admin_show_model_field (mixed $data, mixed $model, ...$args): mixed {
        if (is_callable($data)) {
            $data = call_user_func($data, $model, ...$args);
        } else if(is_string($data) && $model) {
            $data = multi_dot_call($model, $data);
        }

        return $data;
    }
}

if (! function_exists('admin_show_text')) {
    /**
     * Function for generating a string data from input any data.
     *
     * @param  mixed  $data
     * @param  mixed|null  $model
     * @param  mixed  ...$args
     * @return mixed
     */
    function admin_show_text (mixed $data, mixed $model = null, ...$args): mixed {
        if (is_callable($data)) {
            $data = call_user_func($data, $model, ...$args);
        }
        return $data;
    }
}
