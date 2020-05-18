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
    public function input_switcher($field, $value, Model $model, $props = [])
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
     * @param $value
     * @param  array  $props
     * @return string
     */
    public function password_stars($value, $props = [])
    {
        $star = $props[0] ?? '•';
        $id = uniqid('password_');
        $id_showed = "showed_{$id}";
        $stars = str_repeat($star, strlen($value));
        return "<span id='{$id}'><i data-click='0> $::hide 1> $::show' data-params='#{$id} && #{$id_showed}' class='fas fa-eye' style='cursor:pointer'></i> {$stars}</span>".
                "<span id='{$id_showed}' style='display:none'><i data-click='0> $::hide 1> $::show' data-params='#{$id_showed} && #{$id}' class='fas fa-eye-slash' style='cursor:pointer'></i> {$value}</span>";
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
    public function append($value, $props = [])
    {
        $append = implode(" ", $props);

        return $value.$append;
    }

    /**
     * @param $props
     * @param $value
     * @return string
     */
    public function prepend($value, $props = [])
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
        return "<a href='javascript:void(0)' title='Copy to clipboard' data-click='doc::informed_pbcopy' data-params='{$value}'><i class='fas fa-copy'></i></a> " . $value;
    }

    /**
     * @param $value
     * @return string
     */
    public function copied_right($value)
    {
        return $value . " <a href='javascript:void(0)' title='Copy to clipboard' data-click='doc::informed_pbcopy' data-params='{$value}'><i class='fas fa-copy'></i></a>";
    }

    /**
     * @param $value
     * @param  array  $props
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    public function __lang($value, $props = [])
    {
        return __($value, $props);
    }

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
    public function avatar($value, $props = [])
    {
        $size = $props[0] ?? 30;

        if ($value) {
            return "<img src=\"/{$value}\" data-click='fancy::img' data-params='/{$value}' style=\"width:auto;height:auto;max-width:{$size}px;max-height:{$size}px;cursor:pointer\" />";
        } else {
            return "<span class=\"badge badge-dark\">none</span>";
        }
    }

    /**
     * @param $value
     * @param  array  $props
     * @return string
     */
    public function uploaded_file($value, $props = [])
    {
        if ($value) {
            if (is_image(public_path($value))) {
                return $this->avatar($value, $props);
            } else {
                return "<span class=\"badge badge-info\" title='{$value}'>".basename($value)."</span>";
            }
        } else {
            return "<span class=\"badge badge-dark\">none</span>";
        }
    }

    /**
     * @param $value
     * @return string
     */
    public function badge_number($value)
    {
        return $this->badge($value, [$value <= 0 ? 'danger' : 'success']);
    }

    /**
     * @param $props
     * @param $value
     * @return string
     */
    public function badge($value, $props = [])
    {
        $type = $props[0] ?? 'info';
        return "<span class=\"badge badge-{$type}\">{$value}</span>";
    }

    /**
     * @param $value
     * @param $props
     * @return string
     */
    public function pill($value, $props = [])
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
        return $value ? "<span class=\"badge badge-success\">".__('lte.yes')."</span>" :
            "<span class=\"badge badge-danger\">".__('lte.no')."</span>";
    }

    /**
     * @param $value
     * @return string
     */
    public function on_off($value)
    {
        return $value ? "<span class=\"badge badge-success\">".__('lte.on')."</span>" :
            "<span class=\"badge badge-danger\">".__('lte.off')."</span>";
    }

    /**
     * @param $value
     * @param  int[]  $props
     * @return string
     */
    public function fa_icon($value, $props = [])
    {
        $size = $props[0] ?? 22;
        return "<i class='{$value}' title='{$value}' style='font-size: {$size}px'></i>";
    }

    /**
     * @param $value
     * @param  array  $props
     * @return string
     */
    public function badge_tags($value, $props = [])
    {
        $c = collect($value);
        $limit = $props[0] ?? 5;
        return '<span class="badge badge-info">' .
            $c->take($limit)->implode('</span> <span class="badge badge-info">') .
            '</span>' . ($c->count() > $limit ? ' ... <span class="badge badge-warning" title="'.$c->skip($limit)->implode(', ').'">'.($c->count()-$limit).'x</span>' : '');
    }

    /**
     * @param $value
     * @param  array  $props
     * @return string
     */
    public function color_cube($value, $props = [])
    {
        $size = $props[0] ?? 22;
        return "<i class=\"fas fa-square\" title='{$value}' style=\"color: {$value}; font-size: {$size}px\"></i>";
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