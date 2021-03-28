<?php

namespace Lar\LteAdmin\Core\TableExtends;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Tags\A;

/**
 * Class Formatter
 * @package Lar\LteAdmin\Core\TableExtends
 */
class Formatter {

    /**
     * @param $props
     * @param $value
     * @return string
     */
    public function str_limit($value, $props = [])
    {
        $limit = $props[0] ?? 20;
        $str = \Str::limit($value, $limit);

        if ($value == $str) {

            return $str;
        }
        return "<span title='{$value}'>" . $str . "</span>";
    }

    /**
     * @param $value
     * @param  array  $props
     * @param  Model|null  $model
     * @return string
     */
    public function to_append($value, $props = [], Model $model = null)
    {
        if (isset($props[0]) && is_embedded_call($props[0])) {
            $append = call_user_func($props[0], $model);
        } else {
            $append = implode(" ", $props);
            $append = $model ? tag_replace($append, $model) : $append;
        }

        return $value.$append;
    }

    /**
     * @param $value
     * @param  array  $props
     * @param  Model|null  $model
     * @return string
     */
    public function to_prepend($value, $props = [], Model $model = null)
    {
        if (isset($props[0]) && is_embedded_call($props[0])) {
            $prepend = call_user_func($props[0], $model);
        } else {
            $prepend = implode(" ", $props);
            $prepend = $model ? tag_replace($prepend, $model) : $prepend;
        }

        return $prepend.$value;
    }

    /**
     * @param $value
     * @param  array  $props
     * @param  Model|null  $model
     * @return string
     */
    public function to_append_link($value, $props = [], Model $model = null)
    {
        if (!$value) {

            return '<span class="badge badge-dark">NULL</span>';
        }

        $icon = isset($props[0]) ? ($model ? tag_replace($props[0], $model) : $props[0]) : 'fas fa-link';
        $link = isset($props[1]) ? ($model ? tag_replace($props[1], $model) : $props[1]) : 'javascript:void(0)';
        $title = isset($props[2]) ? ($model ? tag_replace($props[2], $model) : $props[2]) : null;

        $link = A::create()->setHref($link)
            ->i([$icon])->_();

        if ($title) {
            $link->attr(['title' => $title]);
        }

        return $value . ' ' . $link;
    }

    /**
     * @param $value
     * @param  array  $props
     * @param  Model|null  $model
     * @return string
     */
    public function to_prepend_link($value, $props = [], Model $model = null)
    {
        if (!$value) {

            return '<span class="badge badge-dark">NULL</span>';
        }

        $icon = isset($props[0]) ? ($model ? tag_replace($props[0], $model) : $props[0]) : 'fas fa-link';
        $link = isset($props[1]) ? ($model ? tag_replace($props[1], $model) : $props[1]) : 'javascript:void(0)';
        $title = isset($props[2]) ? ($model ? tag_replace($props[2], $model) : $props[2]) :  false;

        $link = A::create()->setHref($link)
            ->i([$icon])->_();

        if ($title) {
            $link->attr(['title' => $title]);
        }

        return  $link . ' ' . $value;
    }

    /**
     * @param $props
     * @param $value
     * @return string
     */
    public function number_format($value, $props = [])
    {
        $dec = $props[0] ?? 0;
        $dec_point = $props[1] ?? '.';
        $sep = $props[2] ?? ',';
        $end = $props[3] ?? '';

        return number_format($value, $dec, $dec_point, $sep) . $end;
    }

    /**
     * @param $props
     * @param $value
     * @return string
     */
    public function money($value, $props = [])
    {
        if (!$value) $value = 0;

        return number_format($value, 2, '.', ',') . ' ' . ($props[0] ?? '$');
    }

    /**
     * @param $value
     * @param  array  $props
     * @param  Model|null  $model
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function to_lang($value, $props = [], Model $model = null)
    {
        return $model ? tag_replace(__($value, $props), $model) : __($value, $props);
    }

    /**
     * @param $value
     * @param  array  $props
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function to_string($value, $props = [])
    {
        if (is_object($value)) {
            return  get_class($value);
        } else if (is_array($value)) {
            return json_encode($value);
        } else if ($value===true) {
            return "true";
        } else if ($value===false) {
            return "false";
        } else if ($value===null) {
            return "null";
        }

        return (string)$value;
    }

    /**
     * @param $value
     * @param  array  $props
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function has_lang($value, $props = [])
    {
        return lang_in_text($value);
    }

    /**
     * @param $value
     * @param  array  $props
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function trim($value, $props = [])
    {
        if (isset($props[0])) {

            return trim($value, $props[0]);
        }

        return trim($value);
    }

    /**
     * @param $value
     * @param  array  $props
     * @return string
     */
    public function carbon_format($value, $props = [])
    {
        $format = $props[0] ?? 'Y-m-d H:i:s';

        if ($value instanceof Carbon) {

            return $value->format($format);
        }

        else if (is_numeric($value)) {

            return Carbon::createFromTimestamp($value)->format($format);
        }

        return Carbon::create($value)->format($format);
    }

    /**
     * @param $value
     * @param  array  $props
     * @return string
     */
    public function carbon_time($value, $props = [])
    {
        $format = $props[0] ?? 'H:i:s';
        $time = explode(":", $value);

        return now()
            ->setHour($time[0] ?? 0)
            ->setMinute($time[1] ?? 0)
            ->setSecond($time[2] ?? 0)
            ->format($format);
    }

    /**
     * @param $value
     * @param  array  $props
     * @return mixed|string
     */
    public function explode($value, $props = [])
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
}