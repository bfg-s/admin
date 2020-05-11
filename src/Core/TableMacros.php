<?php

namespace Lar\LteAdmin\Core;

use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Tags\INPUT;
use Lar\LteAdmin\Components\Switcher;

/**
 * Class TableMacros
 * @package Lar\LteAdmin\Core
 */
class TableMacros
{
    /**
     * @param $field_name
     * @param $value
     * @param $props
     * @param $model
     * @return \Lar\Layout\Abstracts\Component
     */
    public function input_switcher($field, $value, $props, Model $model)
    {
        if ($model instanceof Model) {

            $now = lte_now();

            if (isset($now['link.update'])) {

                return Switcher::create($props, ['name' => $field, 'data' => [
                    'size' => 'mini',
                    'mouseup-put' => $now['link.update']($model->getRouteKey()),
                    'mouseup-params' => json_encode([$field => ($value ? 0 : 1)]),
                ]])->setCheckedIf($value, 'checked');
            }

            return Switcher::create($props, ['name' => $field, 'data' => [
                'size' => 'mini',
                'mouseup-jax' => 'lte_admin.custom_save', 'mouseup-props' => [
                    get_class($model),
                    $model->id,
                    $field,
                    '>>$:is(:checked)'
                ]
            ]])->setCheckedIf($value, 'checked');
        }

        return $value;
    }

    /**
     * @param $props
     * @param $value
     * @return string
     */
    public function number_format($props, $value)
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
    public function money($props, $value)
    {
        $dec = $props[0] ?? 2;
        $dec_point = $props[1] ?? '.';
        $sep = $props[2] ?? ',';
        $end = $props[3] ?? ' ';

        return number_format($value, $dec, $dec_point, $sep) . $end;
    }

    /**
     * @param $props
     * @param $value
     * @return string
     */
    public function append($props, $value)
    {
        $append = implode(" ", $props);

        return $value.$append;
    }

    /**
     * @param $props
     * @param $value
     * @return string
     */
    public function prepend($props, $value)
    {
        $prepend = implode(" ", $props);

        return $prepend.$value;
    }

    /**
     * @param $value
     * @return string
     */
    public function copied($value)
    {
        return "<a href='javascript:void(0)' data-click='doc::informed_pbcopy' data-params='{$value}'><i class='fas fa-copy'></i></a> " . $value;
    }

    /**
     * @param $value
     * @return string
     */
    public function copied_right($value)
    {
        return $value . " <a href='javascript:void(0)' data-click='doc::informed_pbcopy' data-params='{$value}'><i class='fas fa-copy'></i></a>";
    }

    /**
     * @param $props
     * @param $value
     * @return string
     */
    public function str_limit($props, $value)
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
    public function avatar($props, $value)
    {
        $size = $props[0] ?? 30;

        if ($value) {
            return "<img src=\"/{$value}\" style=\"width:auto;height:auto;max-width:{$size}px;max-height:{$size}px;\" />";
        } else {
            return "<span class=\"badge badge-dark\">none</span>";
        }
    }

    /**
     * @param $props
     * @param $value
     * @return string
     */
    public function badge($props, $value)
    {
        $type = $props[0] ?? 'info';
        return "<span class=\"badge badge-{$type}\">{$value}</span>";
    }

    /**
     * @param $value
     * @param $props
     * @return string
     */
    public function pill($value, $props)
    {
        $type = $props[0] ?? 'info';
        return "<span class=\"badge badge-pill badge-{$type}\">{$value}</span>";
    }

    /**
     * @param $value
     * @return string
     */
    public function yes_no($value)
    {
        return $value ? "<span class=\"badge badge-success\">Yes</span>" : "<span class=\"badge badge-danger\">No</span>";
    }

    /**
     * @param $value
     * @return \Lar\Layout\Abstracts\Component|mixed|null
     */
    public function true_data($value)
    {
        $return = \Lar\Layout\Tags\SPAN::create(['badge']);

        if (is_null($value)) {

            return $return->addClass('badge-dark')->text("NULL");
        }

        else if ($value === true) {

            return $return->addClass('badge-success')->text('TRUE');
        }

        else if ($value === false) {

            return $return->addClass('badge-danger')->text('FALSE');
        }

        else if (is_array($value)) {

            return $return->addClass('badge-info')->text('Array('.count($value).')');
        }

        else {

            return $value;
        }
    }
}