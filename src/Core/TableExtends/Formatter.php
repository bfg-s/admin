<?php

declare(strict_types=1);

namespace Admin\Core\TableExtends;

use Admin\Models\AdminPermission;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Throwable;

/**
 * Part of the kernel that is responsible for working with formatting columns of the model table.
 */
class Formatter
{
    /**
     * Set the character limit for a line in a column.
     *
     * @param  null  $value
     * @param  array  $props
     * @return string
     * @throws Throwable
     */
    public function str_limit($value = null, array $props = []): string
    {
        $value = $this->strip_tags($value, []);
        $limit = $props[0] ?? 20;
        $str = Str::limit($value, $limit);

        if ($value == $str) {
            return $str;
        }

        return admin_view('components.model-table.formatter.str-limit', [
            'value' => $value,
            'str' => $str,
        ])->render();
    }

    /**
     * Remove all tags from a row in a column.
     *
     * @param $value
     * @param  array  $props
     * @return mixed|string|null
     */
    public function strip_tags($value = null, array $props = []): mixed
    {
        if ($value) {
            $value = strip_tags(
                $this->to_html($value)
            );
        }

        return $value;
    }

    /**
     * Perform decoding of HTML string entities in a column.
     *
     * @param $value
     * @param  array  $props
     * @return mixed|string|null
     */
    public function to_html($value = null, array $props = []): mixed
    {
        if ($value) {
            $value = html_entity_decode($value);
        }

        return $value;
    }

    /**
     * Add resource management buttons (edit, view, entire list) to the value in the column,
     *
     * @param $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @return string
     * @throws Throwable
     */
    public function admin_resource_route($value, array $props = [], Model|array $model = null): string
    {
        if (!isset($props[0]) || !$props[0]) { // route name

            throw new Exception('Enter admin resource name!');
        }
        if (!isset($props[1]) || !$props[1]) { // url param name

            $props[1] = Str::singular($props[0]);
        }
        if (!isset($props[2]) || !$props[2]) { // model param name

            $props[2] = Str::singular($props[0]).'_id';
        }

        $urlIndex = route(
            config('admin.route.name').$props[0].'.index'
        );

        $urlEdit = route(
            config('admin.route.name').$props[0].'.edit',
            [$props[1] => $model ? $model->{$props[2]} : '']
        );

        $urlShow = route(
            config('admin.route.name').$props[0].'.show',
            [$props[1] => $model ? $model->{$props[2]} : '']
        );

        $urlIndex = AdminPermission::checkUrl($urlIndex) ?
            $urlIndex : false;

        $urlEdit = AdminPermission::checkUrl($urlEdit) ?
            $urlEdit : false;

        $urlShow = AdminPermission::checkUrl($urlShow) ?
            $urlShow : false;

        return admin_view('components.model-table.formatter.admin-resource-route', [
            'value' => $value,
            'urlEdit' => $urlEdit,
            'urlShow' => $urlShow,
            'urlIndex' => $urlIndex,
        ])->render();
    }

    /**
     * Add resource control buttons (edit) to the value in the column,
     *
     * @param $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @return string
     * @throws Throwable
     */
    public function admin_resource_route_edit($value, array $props = [], Model|array $model = null): string
    {
        if (!isset($props[0]) || !$props[0]) { // route name

            throw new Exception('Enter admin resource name!');
        }
        if (!isset($props[1]) || !$props[1]) { // url param name

            $props[1] = Str::singular($props[0]);
        }
        if (!isset($props[2]) || !$props[2]) { // model param name

            $props[2] = Str::singular($props[0]).'_id';
        }

        $urlEdit = route(
            config('admin.route.name').$props[0].'.edit',
            [$props[1] => $model ? $model->{$props[2]} : '']
        );

        $urlEdit = AdminPermission::checkUrl($urlEdit) ?
            $urlEdit : false;

        return admin_view('components.model-table.formatter.admin-resource-route', [
            'value' => $value,
            'urlEdit' => $urlEdit,
            'urlShow' => false,
            'urlIndex' => false,
        ])->render();
    }

    /**
     * Add resource control buttons (show) to the value in the column,
     *
     * @param $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @return string
     * @throws Throwable
     */
    public function admin_resource_route_show($value, array $props = [], Model|array $model = null): string
    {
        if (!isset($props[0]) || !$props[0]) { // route name

            throw new Exception('Enter admin resource name!');
        }
        if (!isset($props[1]) || !$props[1]) { // url param name

            $props[1] = Str::singular($props[0]);
        }
        if (!isset($props[2]) || !$props[2]) { // model param name

            $props[2] = Str::singular($props[0]).'_id';
        }

        $urlShow = route(
            config('admin.route.name').$props[0].'.show',
            [$props[1] => $model ? $model->{$props[2]} : '']
        );

        $urlShow = AdminPermission::checkUrl($urlShow) ?
            $urlShow : false;

        return admin_view('components.model-table.formatter.admin-resource-route', [
            'value' => $value,
            'urlEdit' => false,
            'urlShow' => $urlShow,
            'urlIndex' => false,
        ])->render();
    }

    /**
     * Add text to the end of the text that was in the column.
     *
     * @param $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @return string
     */
    public function to_append($value = null, array $props = [], Model|array $model = null): string
    {
        if (isset($props[0]) && is_embedded_call($props[0])) {
            $append = call_user_func($props[0], $model);
        } else {
            $append = implode(' ', $props);
            $append = $model ? tag_replace($append, $model) : $append;
        }

        return $value.$append;
    }

    /**
     * Add text to the beginning of the text that was in the column.
     *
     * @param $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @return string
     */
    public function to_prepend($value = null, array $props = [], Model|array $model = null): string
    {
        if (isset($props[0]) && is_embedded_call($props[0])) {
            $prepend = call_user_func($props[0], $model);
        } else {
            $prepend = implode(' ', $props);
            $prepend = $model ? tag_replace($prepend, $model) : $prepend;
        }

        return $prepend.$value;
    }

    /**
     * Add a link to the end of the text that was in the column.
     *
     * @param  null  $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @return string
     * @throws Throwable
     */
    public function to_append_link($value = null, array $props = [], Model|array $model = null): string
    {
        $icon = isset($props[0]) ? ($model ? tag_replace($props[0], $model) : $props[0]) : 'fas fa-link';
        $link = isset($props[1]) ? ($model ? tag_replace(
            $props[1],
            $model
        ) : $props[1]) : ($value ?: 'javascript:void(0)');
        $title = isset($props[2]) ? ($model ? tag_replace($props[2], $model) : $props[2]) : $link;

        return admin_view('components.model-table.formatter.to-append-link', [
            'value' => $value,
            'icon' => $icon,
            'link' => $link,
            'title' => $title,
        ])->render();
    }

    /**
     * Add a link to the beginning of the text that was in the column.
     *
     * @param  null  $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @return string
     * @throws Throwable
     */
    public function to_prepend_link($value = null, array $props = [], Model|array $model = null): string
    {
        $icon = isset($props[0]) ? ($model ? tag_replace($props[0], $model) : $props[0]) : 'fas fa-link';
        $link = isset($props[1]) ? ($model ? tag_replace(
            $props[1],
            $model
        ) : $props[1]) : ($value ?: 'javascript:void(0)');
        $title = isset($props[2]) ? ($model ? tag_replace($props[2], $model) : $props[2]) : $link;

        return admin_view('components.model-table.formatter.to-append-link', [
            'value' => $value,
            'icon' => $icon,
            'link' => $link,
            'title' => $title,
        ])->render();
    }

    /**
     * Perform formatting of the number that was in the column.
     *
     * @param  array  $props
     * @param $value
     * @return string
     */
    public function number_format($value = null, array $props = []): string
    {
        $dec = $props[0] ?? 0;
        $dec_point = $props[1] ?? '.';
        $sep = $props[2] ?? ',';
        $end = $props[3] ?? '';

        return number_format($value, $dec, $dec_point, $sep).$end;
    }

    /**
     * Format the amount that was in the column into monetary form.
     *
     * @param  array  $props
     * @param $value
     * @return string
     */
    public function money($value = null, array $props = []): string
    {
        if (!$value) {
            $value = 0;
        }

        return number_format($value, 2, '.', ',').' '.($props[0] ?? '$');
    }

    /**
     * Format the number that was in the column into a percentage form.
     *
     * @param  array  $props
     * @param $value
     * @return string
     */
    public function percent($value = null, array $props = []): string
    {
        if (!$value) {
            $value = 0;
        }

        return number_format($value, 2) . ($props[0] ?? '%');
    }

    /**
     * Format the language array that was in the column into the normal form of the current language.
     *
     * @param $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @return array|Application|Translator|string|null
     */
    public function to_lang(
        $value = null,
        array $props = [],
        Model|array $model = null
    ): array|string|Translator|Application|null {
        return $model ? tag_replace(__($value, $props), $model) : __($value, $props);
    }

    /**
     * Format the data that was in the column into a string.
     *
     * @param $value
     * @param  array  $props
     * @return array|Application|Translator|string|null
     */
    public function to_string($value = null, array $props = []): array|string|Translator|Application|null
    {
        if (is_object($value)) {
            return get_class($value);
        } elseif (is_array($value)) {
            return json_encode($value);
        } elseif ($value === true) {
            return 'true';
        } elseif ($value === false) {
            return 'false';
        } elseif ($value === null) {
            return 'null';
        }

        return (string) $value;
    }

    /**
     * Format into a string with the language variables that were in the column.
     *
     * @param $value
     * @param  array  $props
     * @return array|Application|Translator|string|null
     */
    public function has_lang($value = null, array $props = []): array|string|Translator|Application|null
    {
        return lang_in_text($value);
    }

    /**
     * Make a trim with the data that was in the column.
     *
     * @param $value
     * @param  array  $props
     * @return array|Application|Translator|string|null
     */
    public function trim($value = null, array $props = []): array|string|Translator|Application|null
    {
        if (isset($props[0])) {
            return trim($value, $props[0]);
        }

        return trim($value);
    }

    /**
     * Make date formatting carbon what was in the column.
     *
     * @param $value
     * @param  array  $props
     * @return string
     */
    public function carbon_format($value = null, array $props = []): string
    {
        $format = $props[0] ?? 'Y-m-d H:i:s';

        if ($value instanceof Carbon) {
            return $value->format($format);
        } elseif (is_numeric($value)) {
            return Carbon::createFromTimestamp($value)->format($format);
        }

        return Carbon::create($value)->format($format);
    }

    /**
     * Make time formatting carbon that was in the column.
     *
     * @param $value
     * @param  array  $props
     * @return string
     */
    public function carbon_time($value = null, array $props = []): string
    {
        $format = $props[0] ?? 'H:i:s';
        $time = explode(':', $value);

        return now()
            ->setHour($time[0] ?? 0)
            ->setMinute($time[1] ?? 0)
            ->setSecond($time[2] ?? 0)
            ->format($format);
    }

    /**
     * Make an explosion of the data that was in the column.
     *
     * @param $value
     * @param  array  $props
     * @return mixed|string
     */
    public function explode($value = null, array $props = []): mixed
    {
        $delimiter = $props[0] ?? null;

        if ($delimiter) {
            $key = $props[1] ?? 0;

            $exploded = explode($delimiter, $value);

            if (isset($exploded[$key])) {
                $value = $exploded[$key];
            }
        }

        return $value;
    }

    /**
     * Format the date that was in the column into a beautiful form.
     *
     * @param $value
     * @return string
     */
    public function beautiful_date($value = null): string
    {
        return beautiful_date($value);
    }

    /**
     * Format the date and time that was in the column into a beautiful form.
     *
     * @param $value
     * @return string
     */
    public function beautiful_date_time($value = null): string
    {
        return beautiful_date_time($value);
    }

    /**
     * Format what was in the column into Json.
     *
     * @param $value
     * @return mixed|string|null
     */
    public function to_json($value = null): mixed
    {
        if ($value) {
            if (is_string($value)) {
                $result = json_decode(
                    htmlspecialchars_decode($value), true
                );

                if (json_last_error() === JSON_ERROR_NONE) {
                    $value = $result;
                }
            }

            if (is_array($value)) {
                return "<pre>".json_encode($value, JSON_PRETTY_PRINT)."</pre>";
            }
        }

        return $value;
    }
}
