<?php

namespace Admin\Jax;

use Cookie;
use DB;
use Excel;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Admin\Components\LiveComponent;
use Admin\Components\ModalComponent;
use Admin\Core\PrepareExport;
use Throwable;

class Admin extends AdminExecutor
{
    /**
     * @var array
     */
    public static array $callbacks = [

    ];
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
     * @throws Throwable
     */
    public function nestable_save(
        string $model,
        int $depth = 1,
        $data = [],
        string $parent_field = null,
        string $order_field = 'order'
    ) {
        if (!check_referer('PUT')) {
            return [];
        }

        if (class_exists($model)) {
            DB::transaction(function () use ($model, $depth, $data, $parent_field, $order_field) {
                foreach ($this->nestable_collapse($data, $depth, $parent_field, null, $order_field) as $item) {
                    /** @var Model $model */
                    if ($model = $model::where('id', $item['id'])->first()) {
                        $model->update($item['data']);
                    }
                }
            });
            static::$i = 0;
            $this->toast_success(__('admin.saved_successfully'));
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
    private function nestable_collapse(
        array $data,
        int $depth,
        string $parent_field = null,
        $parent = null,
        string $order_field = 'order'
    ) {
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
                $result = array_merge(
                    $result,
                    $this->nestable_collapse($item['children'], $depth, $parent_field, $item['id'], $order_field)
                );
            }
        }

        return $result;
    }

    /**
     * @param  string|null  $model
     * @param  int|null  $id
     * @param  string|null  $field_name
     * @param  mixed  $val
     * @return array
     */
    public function custom_save(string $model = null, int $id = null, string $field_name = null, $val = false)
    {
        if (!check_referer('PUT')) {
            return [];
        }

        /** @var Model $find */
        if ($model && class_exists($model) && $id && $field_name && $find = $model::find($id)) {
            if ($find) {
                $find->{$field_name} = $val;

                if ($find->save()) {
                    $this->put('alert::success', __('admin.saved_successfully'))->reload();
                } else {
                    $this->put('alert::error', __('admin.unknown_error'));
                }
            }
        }
    }

    /**
     * @param  string  $class
     * @param  array  $ids
     * @return array
     * @throws Exception
     */
    public function mass_delete(string $class, array $ids)
    {
        if (!check_referer('DELETE')) {
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
                $this->put('alert::success', __('admin.successfully_deleted'))->reload();
            } else {
                $this->put('alert::error', __('admin.unknown_error'));
            }
        } else {
            $this->put('alert::error', __('admin.unknown_error'));
        }
    }

    public function load_lives()
    {
        $this->refererEmit();

        $result_areas = [];

        foreach (LiveComponent::$list as $area => $item) {
            $content = $item->getRenderContent();
            $result_areas[$area] = [
                'hash' => sha1($content),
                'content' => $content,
            ];
        }

        return $result_areas;
    }

    /**
     * @return array|void
     */
    public function load_modal()
    {
        if (!check_referer()) {
            return [];
        }

        $this->refererEmit();

        $modal = ModalComponent::$list[request('_modal')] ?? null;

        if ($modal) {
            if (request()->has('_modal_submit')) {
                return $modal->submitEvent ? app()->call($modal->submitEvent) : [];
            }

            return [
                'size' => $modal->size,
                'backdrop' => $modal->backdrop,
                'temporary' => $modal->temporary,
                'content' => $modal->getRendered(),
            ];
        }

        abort(404);
    }

    public function export_excel(string $model, array $ids, string $order, string $order_type, string $table)
    {
        if (!check_referer()) {
            return [];
        }

        $this->refererEmit();

        $prepared = new PrepareExport($model, $ids, $order, $order_type, $table);

        return Excel::download($prepared, class_basename($model).'_'.now()->format('Y_m_d_His').'.xlsx');
    }

    public function export_csv(string $model, array $ids, string $order, string $order_type, string $table)
    {
        if (!check_referer()) {
            return [];
        }

        $this->refererEmit();

        $prepared = new PrepareExport($model, $ids, $order, $order_type, $table);

        return Excel::download($prepared, class_basename($model).'_'.now()->format('Y_m_d_His').'.csv');
    }

    public function call_callback(int $key, array $parameters)
    {
        if (!check_referer()) {
            return [];
        }

        $this->refererEmit();

        if (isset(static::$callbacks[$key])) {
            app()->call(static::$callbacks[$key], $parameters);
        } else {
            $this->toast_error(__('admin.callback_not_found'));
        }
    }

    public function toggle_dark()
    {
        Cookie::queue(
            'admin-dark-mode',
            (int) !admin_repo()->isDarkMode,
            time() * 2
        );
        $this->put('window.location.reload');
    }
}
