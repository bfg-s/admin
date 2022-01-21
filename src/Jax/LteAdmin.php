<?php

namespace Lar\LteAdmin\Jax;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Lar\LteAdmin\Controllers\ModalController;
use Lar\LteAdmin\Core\PrepareExport;
use Lar\LteAdmin\LteBoot;

class LteAdmin extends LteAdminExecutor
{
    /**
     * @var int
     */
    protected static $i = 0;

    /**
     * @param  string  $model
     * @param  int  $depth
     * @param  array  $data
     * @param  string|null  $parent_field
     * @param  string  $order_field
     * @return array
     * @throws \Throwable
     */
    public function nestable_save(string $model, int $depth = 1, $data = [], string $parent_field = null, string $order_field = 'order')
    {
        if (! check_referer('PUT')) {
            return [];
        }

        if (class_exists($model)) {
            \DB::transaction(function () use ($model, $depth, $data, $parent_field, $order_field) {
                foreach ($this->nestable_collapse($data, $depth, $parent_field, null, $order_field) as $item) {
                    /** @var Model $model */
                    if ($model = $model::where('id', $item['id'])->first()) {
                        $model->update($item['data']);
                    }
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
     * @param  string  $order_field
     * @return array
     */
    private function nestable_collapse(array $data, int $depth, string $parent_field = null, $parent = null, string $order_field = 'order')
    {
        $result = [];

        foreach ($data as $item) {
            $new = [];

            $new['id'] = $item['id'];

            if ($depth > 1) {
                $new['data'][$parent_field ?? 'parent_id'] = $parent;
            }

            $new['data'][$order_field ?? 'order'] = static::$i;

            $result[] = $new;

            static::$i++;

            if (isset($item['children'])) {
                $result = array_merge($result, $this->nestable_collapse($item['children'], $depth, $parent_field, $item['id'], $order_field));
            }
        }

        return $result;
    }

    /**
     * @param  string|null  $model
     * @param  int|null  $id
     * @param  string|null  $field_name
     * @param  bool  $val
     * @return array
     */
    public function custom_save(string $model = null, int $id = null, string $field_name = null, bool $val = false)
    {
        if (! check_referer('PUT')) {
            return [];
        }

        /** @var Model $find */
        if ($model && class_exists($model) && $id && $field_name && $find = $model::find($id)) {
            if ($find) {
                $find->{$field_name} = $val;

                if ($find->save()) {
                    $this->toast_success(__('lte.saved_successfully'));
                } else {
                    $this->toast_error(__('lte.unknown_error'));
                }
            }
        }
    }

    /**
     * @param  string  $class
     * @param  array  $ids
     * @return array
     * @throws \Exception
     */
    public function mass_delete(string $class, array $ids)
    {
        if (! check_referer('DELETE')) {
            return [];
        }

        /** @var Model $class */
        if (class_exists($class) && method_exists($class, 'delete')) {
            $success = false;
            foreach ($class::whereIn('id', $ids)->get() as $item) {
                if ($item->delete()) {
                    $success = true;
                } else {
                    $success = false;
                }
            }
            if ($success) {
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
     * @return \Lar\LteAdmin\Components\ModalComponent|mixed
     */
    public function load_modal(string $handle, array $params = [])
    {
        LteBoot::run();

        if (strpos($handle, '@') !== false) {
            $handle = Str::parseCallback($handle, 'index');
            if (! class_exists($handle[0])) {
                if (class_exists(lte_app_namespace("Modals\\{$handle[0]}"))) {
                    $handle[0] = lte_app_namespace("Modals\\{$handle[0]}");
                } else {
                    abort(404);
                }
            }

            return embedded_call($handle, $params);
        } elseif (strpos($handle, '::') !== false) {
            $handle = explode('::', $handle);
            if (! class_exists($handle[0])) {
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

    public function export_excel(string $model, array $ids, string $order, string $order_type)
    {
        $prepared = new PrepareExport($model, $ids, $order, $order_type);

        return \Excel::download($prepared, class_basename($model).'_'.now()->format('Y_m_d_His').'.xlsx');
    }

    public function export_csv(string $model, array $ids, string $order, string $order_type)
    {
        $prepared = new PrepareExport($model, $ids, $order, $order_type);

        return \Excel::download($prepared, class_basename($model).'_'.now()->format('Y_m_d_His').'.csv');
    }
}
