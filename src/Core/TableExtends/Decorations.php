<?php

namespace Admin\Core\TableExtends;

use Admin\Components\Component;
use Admin\Components\FieldComponent;
use Admin\Components\Inputs\RatingInput;
use Admin\Components\Small\SpanComponent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class Decorations
{
    /**
     * @param $value
     * @param  array  $props
     * @return FieldComponent|RatingInput
     */
    public function rating_stars($value, array $props = []): FieldComponent|RatingInput
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
     * @return string
     * @throws Throwable
     */
    public function password_stars($value, array $props = []): string
    {
        if (!$value) {
            return $this->true_data($value);
        }
        $star = $props[0] ?? '•';
        $id = uniqid('password_');
        $id_showed = "showed_{$id}";
        $stars = str_repeat($star, strlen(strip_tags(html_entity_decode($value))));

        return admin_view('components.model-table.decorations.password-stars', [
            'id' => $id,
            'id_showed' => $id_showed,
            'stars' => $stars,
            'value' => $value,
        ])->render();
    }

    /**
     * @param $value
     * @return string
     * @throws Throwable
     */
    public function true_data($value): string
    {
        return admin_view('components.model-table.decorations.true-data', [
            'value' => $value
        ])->render();
    }

    /**
     * @param $value
     * @param  array  $props
     * @return string
     * @throws Throwable
     */
    public function uploaded_file($value, array $props = []): string
    {
        if ($value && is_image(public_path($value))) {
            return $this->avatar($value, $props);
        }

        return admin_view('components.model-table.decorations.uploaded-file', [
            'value' => $value,
        ])->render();
    }

    /**
     * @param $value
     * @param  array  $props
     * @return string
     * @throws Throwable
     */
    public function avatar($value, array $props = []): string
    {
        $size = $props[0] ?? 30;

        return admin_view('components.model-table.decorations.avatar', [
            'value' => $value ? (!str_starts_with($value, 'http') ? '/'.trim($value, '/') : $value) : null,
            'size' => $size
        ])->render();
    }

    /**
     * @param $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @return string
     * @throws Throwable
     */
    public function copied($value, array $props = [], Model|array $model = null): string
    {
        if (isset($props[0]) && is_embedded_call($props[0])) {
            $value_for_copy = call_user_func($props[0], $model);
        }

        if (!$value) {
            return $this->true_data($value);
        }

        return admin_view('components.model-table.decorations.copied', [
            'value_before' => true,
            'value_for_copy' => $value_for_copy ?? $value,
            'value' => $value,
        ])->render();
    }

    /**
     * @param $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @return string|Component|null
     * @throws Throwable
     */
    public function copied_right($value, array $props = [], Model|array $model = null): string|Component|null
    {
        if (isset($props[0]) && is_embedded_call($props[0])) {
            $value_for_copy = call_user_func($props[0], $model);
        }

        if (!$value) {
            return $this->true_data($value);
        }

        return admin_view('components.model-table.decorations.copied', [
            'value_before' => false,
            'value_for_copy' => $value_for_copy ?? $value,
            'value' => $value,
        ])->render();
    }

    /**
     * @param $value
     * @return string
     * @throws Throwable
     */
    public function badge_number($value): string
    {
        return $this->badge($value ?: 0, [$value <= 0 ? 'danger' : 'success']);
    }

    /**
     * @param $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @return string
     * @throws Throwable
     */
    public function badge($value, array $props = [], Model|array $model = null): string
    {
        $type = $props[0] ?? 'info';
        if (is_embedded_call($type)) {
            $type = call_user_func($type, $model);
        }

        return admin_view('components.model-table.decorations.badge', [
            'type' => $type,
            'value' => $value,
        ])->render();
    }

    /**
     * @param $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @return string
     * @throws Throwable
     */
    public function pill($value, array $props = [], Model|array $model = null): string
    {
        $type = $props[0] ?? 'info';
        if (is_embedded_call($type)) {
            $type = call_user_func($type, $model);
        }

        return admin_view('components.model-table.decorations.pill', [
            'type' => $type,
            'value' => $value,
        ])->render();
    }

    /**
     * @param $value
     * @return string
     * @throws Throwable
     */
    public function yes_no($value): string
    {
        return admin_view('components.model-table.decorations.yes-no', [
            'value' => $value,
        ])->render();
    }

    /**
     * @param $value
     * @return string
     * @throws Throwable
     */
    public function on_off($value): string
    {
        return admin_view('components.model-table.decorations.on-off', [
            'value' => $value,
        ])->render();
    }

    /**
     * @param $value
     * @param  int[]  $props
     * @return string
     * @throws Throwable
     */
    public function fa_icon($value, array $props = []): string
    {
        $size = $props[0] ?? 22;

        return admin_view('components.model-table.decorations.fa-icon', [
            'value' => $value,
            'size' => $size,
        ])->render();
    }

    /**
     * @param $value
     * @param  array  $props
     * @return string
     * @throws Throwable
     */
    public function badge_tags($value, array $props = []): string
    {
        $c = collect($value);
        $limit = $props[0] ?? 5;

        return admin_view('components.model-table.decorations.badge-tags', [
            'collect' => $c,
            'limit' => $limit,
        ])->render();
    }

    /**
     * @param $value
     * @param  array  $props
     * @return string
     * @throws Throwable
     */
    public function color_cube($value, array $props = []): string
    {
        $size = $props[0] ?? 22;

        return admin_view('components.model-table.decorations.color-cube', [
            'value' => $value,
            'size' => $size,
        ])->render();
    }

    /**
     * @param $value
     * @param  array  $props
     * @return string
     * @throws Throwable
     */
    public function progress_complete($value, array $props = []): string
    {
        $text = $props[0] ?? (__('admin.complete') ?? 'Complete');

        return admin_view('components.model-table.decorations.progress-complete', [
            'value' => $value,
            'text' => $text,
        ])->render();
    }
}
