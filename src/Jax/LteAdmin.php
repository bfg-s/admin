<?php

namespace Lar\LteAdmin\Jax;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Lar\LJS\JaxExecutor;
use Lar\LteAdmin\Controllers\ModalController;
use Lar\LteAdmin\LteBoot;
use Lar\LteAdmin\Models\LteFunction;
use Lar\LteAdmin\Resources\LteFunctionResource;

/**
 * Class LteAdmin
 * @package App\Http\JaxExecutors
 */
class LteAdmin extends LteAdminExecutor
{
    /**
     * @var int
     */
    static protected $i = 0;

    /**
     * @param  string  $model
     * @param  int  $depth
     * @param  array  $data
     * @param  string|null  $parent_field
     */
    public function nestable_save(string $model, int $depth = 1, $data = [], string $parent_field = null)
    {
        if (class_exists($model)) {
            \DB::transaction(function () use ($model, $depth, $data, $parent_field) {
                foreach ($this->nestable_collapse($data, $depth, $parent_field) as $item) {
                    $model::where('id', $item['id'])->update($item['data']);
                }
            });
            static::$i = 0;
        }
    }

    /**
     * @param  array  $data
     * @param  int  $depth
     * @param  string|null  $parent_field
     * @param  null  $parent
     * @param  int  $i
     * @return array
     */
    private function nestable_collapse(array $data, int $depth, string $parent_field = null, $parent = null)
    {
        $result = [];

        foreach ($data as $item) {

            $new = [];

            $new['id'] = $item['id'];

            if ($depth > 1) {

                $new['data']['parent_id'] = $parent;
            }

            $new['data']['order'] = static::$i;

            $result[] = $new;

            static::$i++;

            if (isset($item['children'])) {

                $result = array_merge($result, $this->nestable_collapse($item['children'], $depth, $parent_field, $item['id']));
            }
        }

        return $result;
    }

    /**
     * @param  string|null  $model
     * @param  int|null  $id
     * @param  string|null  $field_name
     * @param  bool  $val
     */
    public function custom_save(string $model = null, int $id = null, string $field_name = null, bool $val = false)
    {
        /** @var Model $find */
        if ($model && class_exists($model) && $id && $field_name && $find = $model::find($id)) {

            if ($find) {

                $find->{$field_name} = $val;

                if ($find->save()) {

                    $this->toast_success(__('lte.saved_successfully'));
                }

                else {

                    $this->toast_error(__('lte.unknown_error'));
                }
            }
        }
    }

    /**
     * @param  string  $class
     * @param  array  $ids
     */
    public function mass_delete(string $class, array $ids)
    {
        /** @var Model $class */
        if (class_exists($class) && method_exists($class, 'delete')) {
            if ($class::whereIn('id', $ids)->delete()) {
                $this->toast_success(__('lte.successfully_deleted'))->reload();
            } else {
                $this->toast_error(__('lte.unknown_error'));
            }

        } else {
            $this->toast_error(__('lte.unknown_error'));
        }
    }

    /**
     * @param  string  $handle
     * @param  array  $params
     */
    public function load_modal(string $handle, array $params = [])
    {
        LteBoot::run();

        if (strpos($handle, '@') !== false) {
            $handle = Str::parseCallback($handle, 'index');
            if (!class_exists($handle[0])) {
                if (class_exists(lte_app_namespace("Modals\\{$handle[0]}"))) {
                    $handle[0] = lte_app_namespace("Modals\\{$handle[0]}");
                } else {
                    abort(404);
                }
            }
            return embedded_call($handle, $params);
        }

        else if (strpos($handle, '::') !== false) {

            $handle = explode('::', $handle);
            if (!class_exists($handle[0])) {
                if (class_exists(lte_app_namespace($handle[0]))) {
                    $handle[0] = lte_app_namespace($handle[0]);
                } else {
                    abort(404);
                }
            }
            return (new ModalController())->setCreate($handle)->index();
        }

        abort(404);
    }
}
