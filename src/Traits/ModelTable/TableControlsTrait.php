<?php

namespace Admin\Traits\ModelTable;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Tags\SPAN;
use Admin\Components\ButtonsComponent;
use Admin\Core\ModelTableAction;
use Admin\Core\PrepareExport;

trait TableControlsTrait
{
    /**
     * @var Closure|array|null
     */
    protected $controls = null;

    /**
     * @var Closure|array|null
     */
    protected $control_info = null;

    /**
     * @var Closure|array|null
     */
    protected $control_edit = null;

    /**
     * @var Closure|array|null
     */
    protected $control_delete = null;

    /**
     * @var Closure|array|null
     */
    protected $control_force_delete = null;

    /**
     * @var Closure|array|null
     */
    protected $control_restore = null;

    /**
     * @var Closure|array|null
     */
    protected $control_selectable = null;

    /**
     * @var bool
     */
    protected $checks = true;

    /**
     * @var bool
     */
    protected $check_delete = null;
    /**
     * @var array
     */
    protected $action = [];

    /**
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlGroup($test = null)
    {
        $this->set_test_var('controls', $test);

        return $this;
    }

    /**
     * @param  string  $var_name
     * @param $test
     */
    protected function set_test_var(string $var_name, $test)
    {
        if (is_embedded_call($test)) {
            $this->{$var_name} = $test;
        } else {
            $this->{$var_name} = static function () use ($test) {
                return (bool) $test;
            };
        }
    }

    /**
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlInfo($test = null)
    {
        $this->set_test_var('control_info', $test);

        return $this;
    }

    /**
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlEdit($test = null)
    {
        $this->set_test_var('control_edit', $test);

        return $this;
    }

    /**
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlDelete($test = null)
    {
        $this->set_test_var('control_delete', $test);

        return $this;
    }

    /**
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlForceDelete($test = null)
    {
        $this->set_test_var('control_force_delete', $test);

        return $this;
    }

    /**
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlRestore($test = null)
    {
        $this->set_test_var('control_restore', $test);

        return $this;
    }

    /**
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlSelect($test = null)
    {
        $this->set_test_var('control_selectable', $test);

        return $this;
    }

    /**
     * @param  null  $test
     * @return $this
     */
    public function checkDelete($test = null)
    {
        $this->set_test_var('check_delete', $test);

        return $this;
    }

    /**
     * @return $this
     */
    public function disableChecks()
    {
        $this->checks = false;

        return $this;
    }

    public function action(callable $callback, array $parameter = []): ModelTableAction
    {
//        $this->action[] = [
//            'jax' => $this->registerCallBack($callback),
//            'title' => $title,
//            'icon' => $icon,
//            'confirm' => $confirm,
//            'warning' => $warning,
//        ];

        return $this->action[] = new ModelTableAction(
            $this->model,
            $callback,
            $parameter
        );
    }

    public function getActionData()
    {
        $this->getModelName();
        $this->model_class = $this->realModel() ? get_class($this->realModel()) : null;
        $hasDelete = $this->menu
            && $this->get_test_var('check_delete')
            && $this->menu->isResource()
            && $this->menu->getLinkDestroy(0);
        $select_type = request()->get($this->model_name.'_type', $this->order_type);
        $this->order_field = request()->get($this->model_name, $this->order_field);

        return [
            'table_id' => $this->model_name,
            'object' => $this->model_class,
            'hasHidden' => $this->hasHidden,
            'hasDelete' => $hasDelete,
            'show' => (count($this->action) || $hasDelete || count(PrepareExport::$columns) || $this->hasHidden) && $this->checks,
            'actions' => array_map(fn(ModelTableAction $action) => $action->toArray(), $this->action),
            'order_field' => $this->order_field,
            'select_type' => $select_type,
            'columns' => collect($this->columns)
                ->filter(static function ($i) {
                    return isset($i['field']) && is_string($i['field']) && !$i['hide'];
                })
                ->pluck('field')
                ->toArray(),
            'all_columns' => collect($this->columns)
                ->filter(static function ($i) {
                    return isset($i['label']) && $i['label'];
                })
                ->map(static function ($i) {
                    unset($i['macros']);

                    return $i;
                })
                ->toArray(),
        ];
    }

    /**
     * @param  string  $var_name
     * @param  array  $args
     * @return bool
     */
    protected function get_test_var(string $var_name, array $args = [])
    {
        if ($this->{$var_name} !== null) {
            return call_user_func_array($this->{$var_name}, $args);
        }

        return true;
    }

    /**
     * Create default controls.
     */
    protected function _create_controls()
    {
        if ($this->get_test_var('controls')) {
            $hasDelete = $this->menu
                && $this->menu->isResource()
                && $this->menu->getLinkDestroy(0);
            $show = count($this->action) || $hasDelete || count(PrepareExport::$columns) || $this->hasHidden;
            $modelName = $this->model_name;

            if ($this->checks && !request()->has('show_deleted') && $show) {
                $this->to_prepend()->column(function (SPAN $span) use ($hasDelete) {
                    $span->_addClass('fit');
                    $span->view('admin::segment.model_table_checkbox', [
                        'id' => false,
                        'table_id' => $this->model_name,
                        'object' => $this->model_class,
                        'actions' => $this->action,
                        'delete' => $this->get_test_var('check_delete') && $hasDelete,
                        'columns' => collect($this->columns)->filter(static function ($i) {
                            return isset($i['field']) && is_string($i['field']);
                        })->pluck('field')->toArray(),
                    ])->render();
                }, function (Model $model) use ($modelName) {
                    return view('admin::segment.model_table_checkbox', [
                        'id' => $model->id,
                        'table_id' => $modelName,
                        'disabled' => !$this->get_test_var('control_selectable', [$model]),
                    ])->render();
                });
            }

            if (request()->has('show_deleted')) {
                $this->deleted_at();
            }

            $this->column(static function (SPAN $span) {
                $span->_addClass('fit');
            }, function (Model $model) {
                $menu = $this->menu;

                return ButtonsComponent::create()->when(function (ButtonsComponent $group) use ($model, $menu) {
                    if ($menu && $menu->isResource()) {
                        $key = $model->getRouteKey();

                        if (!request()->has('show_deleted')) {
                            if ($this->get_test_var('control_edit', [$model])) {
                                $group->resourceEdit($menu->getLinkEdit($key), '');
                            }

                            if ($this->get_test_var('control_delete', [$model])) {
                                $group->resourceDestroy(
                                    $menu->getLinkDestroy($key),
                                    '',
                                    $model->getRouteKeyName(),
                                    $key
                                );
                            }

                            if ($this->get_test_var('control_info', [$model])) {
                                $group->resourceInfo($menu->getLinkShow($key), '');
                            }
                        } else {
                            if ($this->get_test_var('control_restore', [$model])) {
                                $group->resourceRestore(
                                    $menu->getLinkDestroy($key),
                                    '',
                                    $model->getRouteKeyName(),
                                    $key
                                );
                            }

                            if ($this->get_test_var('control_force_delete', [$model])) {
                                $group->resourceForceDestroy(
                                    $menu->getLinkDestroy($key),
                                    '',
                                    $model->getRouteKeyName(),
                                    $key
                                );
                            }
                        }
                    }
                });
            });
        }
    }
}
