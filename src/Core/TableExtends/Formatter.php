<?php

namespace Lar\LteAdmin\Core\TableExtends;

use Carbon\Carbon;

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
     * @param $props
     * @param $value
     * @return string
     */
    public function to_append($value, $props = [])
    {
        $append = implode(" ", $props);

        return $value.$append;
    }

    /**
     * @param $props
     * @param $value
     * @return string
     */
    public function to_prepend($value, $props = [])
    {
        $prepend = implode(" ", $props);

        return $prepend.$value;
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
        return number_format($value, 2, '.', ',') . ' ' . ($props[0] ?? '$');
    }

    /**
     * @param $value
     * @param  array  $props
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function to_lang($value, $props = [])
    {
        return __($value, $props);
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
}