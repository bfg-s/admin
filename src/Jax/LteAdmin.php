<?php

namespace Lar\LteAdmin\Jax;

use Illuminate\Database\Eloquent\Model;
use Lar\LJS\JaxExecutor;

/**
 * Class LteAdmin
 * @package App\Http\JaxExecutors
 */
class LteAdmin extends JaxExecutor
{
    /**
     * @var int
     */
    static protected $i = 0;

    /**
     * @var string
     */
    public static $name = "lte_admin";

    /**
     * Public method access
     * 
     * @return bool
     */
    public function access() {
        
        return !\LteAdmin::guest();
    }

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
}
