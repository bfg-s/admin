<?php

namespace Admin\Core\TableExtends;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\SPAN;
use Admin\Components\FieldComponent;
use Admin\Components\Fields\RatingField;

class Decorations
{
    /**
     * @param $value
     * @param  array  $props
     * @return FieldComponent|RatingField
     */
    public function rating_stars($value, array $props = []): FieldComponent|RatingField
    {
        return FieldComponent::rating('rating')
            ->only_input()
            ->readonly()
            ->value($value)
            ->sizeXs();
    }

    /**
     * @param $value
     * @param  array  $props
     * @return string|Component|null
     */
    public function password_stars($value, array $props = []): string|Component|null
    {
        if (!$value) {
            return $this->true_data($value);
        }
        $star = $props[0] ?? '•';
        $id = uniqid('password_');
        $id_showed = "showed_{$id}";
        $stars = str_repeat($star, strlen(strip_tags(html_entity_decode($value))));

        return "<span id='{$id}'><i data-click='0> $::hide 1> $::show' data-params='#{$id} && #{$id_showed}' class='fas fa-eye' style='cursor:pointer'></i> {$stars}</span>".
            "<span id='{$id_showed}' style='display:none'><i data-click='0> $::hide 1> $::show' data-params='#{$id_showed} && #{$id}' class='fas fa-eye-slash' style='cursor:pointer'></i> {$value}</span>";
    }

    /**
     * @param $value
     * @return Component|mixed|null
     */
    public function true_data($value): mixed
    {
        $return = SPAN::create(['badge']);

        if (is_null($value) || $value === '') {
            return $return->addClass('badge-dark')->text('NULL');
        } elseif ($value === true) {
            return $return->addClass('badge-success')->text('TRUE');
        } elseif ($value === false) {
            return $return->addClass('badge-danger')->text('FALSE');
        } elseif (is_array($value)) {
            return $return->addClass('badge-info')->text('Array('.count($value).')');
        } elseif ($value instanceof Carbon) {
            return $value->format('Y-m-d H:i:s');
        } else {
            return $value;
        }
    }

    /**
     * @param $value
     * @param  array  $props
     * @return string
     */
    public function uploaded_file($value, array $props = []): string
    {
        if ($value) {
            if (is_image(public_path($value))) {
                return $this->avatar($value, $props);
            } else {
                return "<span class=\"badge badge-info\" title='{$value}'>".basename($value).'</span>';
            }
        } else {
            return '<span class="badge badge-dark">none</span>';
        }
    }

    /**
     * @param  array  $props
     * @param $value
     * @return string
     */
    public function avatar($value, array $props = []): string
    {
        $size = $props[0] ?? 30;

        if ($value) {
            if (!str_starts_with($value, 'http')) {
                $value = '/'.trim($value, '/');
            }

            return "<img src=\"{$value}\" data-click='fancy::img' data-params='{$value}' style=\"width:auto;height:auto;max-width:{$size}px;max-height:{$size}px;cursor:pointer\"  alt='avatar'/>";
        } else {
            return '<span class="badge badge-dark">none</span>';
        }
    }

    /**
     * @param $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @return string|Component|null
     */
    public function copied($value, array $props = [], Model|array $model = null): string|Component|null
    {
        if (isset($props[0]) && is_embedded_call($props[0])) {
            $value_for_copy = call_user_func($props[0], $model);
        }

        if (!$value) {
            return $this->true_data($value);
        }

        return "<a href='javascript:void(0)' class='d-none d-sm-inline' title='Copy to clipboard' data-click='doc::informed_pbcopy' data-params='".strip_tags(html_entity_decode($value_for_copy ?? $value))."'><i class='fas fa-copy'></i></a> ".$value;
    }

    /**
     * @param $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @return string|Component|null
     */
    public function copied_right($value, array $props = [], Model|array $model = null): string|Component|null
    {
        if (isset($props[0]) && is_embedded_call($props[0])) {
            $value_for_copy = call_user_func($props[0], $model);
        }

        if (!$value) {
            return $this->true_data($value);
        }

        return $value." <a href='javascript:void(0)' class='d-none d-sm-inline' title='Copy to clipboard' data-click='doc::informed_pbcopy' data-params='".strip_tags(html_entity_decode($value_for_copy ?? $value))."'><i class='fas fa-copy'></i></a>";
    }

    /**
     * @param $value
     * @return string
     */
    public function badge_number($value): string
    {
        return $this->badge($value, [$value <= 0 ? 'danger' : 'success']);
    }

    /**
     * @param $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @return string
     */
    public function badge($value, array $props = [], Model|array $model = null): string
    {
        $type = $props[0] ?? 'info';
        if (is_embedded_call($type)) {
            $type = call_user_func($type, $model);
        }

        return "<span class=\"badge badge-{$type}\">{$value}</span>";
    }

    /**
     * @param $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @return string
     */
    public function pill($value, array $props = [], Model|array $model = null): string
    {
        $type = $props[0] ?? 'info';
        if (is_embedded_call($type)) {
            $type = call_user_func($type, $model);
        }

        return "<span class=\"badge badge-pill badge-{$type}\">{$value}</span>";
    }

    /**
     * @param $value
     * @return string
     */
    public function yes_no($value): string
    {
        return $value ? '<span class="badge badge-success">'.__('admin.yes').'</span>' :
            '<span class="badge badge-danger">'.__('admin.no').'</span>';
    }

    /**
     * @param $value
     * @return string
     */
    public function on_off($value): string
    {
        return $value ? '<span class="badge badge-success">'.__('admin.on').'</span>' :
            '<span class="badge badge-danger">'.__('admin.off').'</span>';
    }

    /**
     * @param $value
     * @param  int[]  $props
     * @return string
     */
    public function fa_icon($value, array $props = []): string
    {
        $size = $props[0] ?? 22;

        return "<i class='{$value}' title='{$value}' style='font-size: {$size}px'></i>";
    }

    /**
     * @param $value
     * @param  array  $props
     * @return string
     */
    public function badge_tags($value, array $props = []): string
    {
        $c = collect($value);
        $limit = $props[0] ?? 5;

        return '<span class="badge badge-info">'.
            $c->take($limit)->implode('</span> <span class="badge badge-info">').
            '</span>'.($c->count() > $limit ? ' ... <span class="badge badge-warning" title="'.$c->skip($limit)->implode(', ').'">'.($c->count() - $limit).'x</span>' : '');
    }

    /**
     * @param $value
     * @param  array  $props
     * @return string
     */
    public function color_cube($value, array $props = []): string
    {
        $size = $props[0] ?? 22;

        return "<i class=\"fas fa-square\" title='{$value}' style=\"color: {$value}; font-size: {$size}px\"></i>";
    }

    /**
     * @param $value
     * @param  array  $props
     * @return string
     */
    public function progress_complete($value, array $props = []): string
    {
        $text = $props[0] ?? (__('admin.complete') ?? 'Complete');

        return '<div class="progress progress-sm">
                    <div class="progress-bar bg-green" role="progressbar" aria-valuenow="'.$value.'" aria-valuemin="0" aria-valuemax="100" style="width: '.$value.'%">
                    </div>
                </div>' . ($text ? '<small>'.explode('.', round($value))[0].'% '.$text.'</small>':'');
    }
}
