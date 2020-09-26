<?php

namespace Lar\LteAdmin\Segments\Tagable\Traits\ModelTable;

use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Tags\SPAN;
use Lar\LteAdmin\Segments\Tagable\ButtonGroup;

/**
 * Trait TableControlsTrait
 * @package Lar\LteAdmin\Segments\Tagable\Traits\ModelTable
 */
trait TableControlsTrait {

    /**
     * @var \Closure|array|null
     */
    protected $controls = null;

    /**
     * @var \Closure|array|null
     */
    protected $control_info = null;

    /**
     * @var \Closure|array|null
     */
    protected $control_edit = null;

    /**
     * @var \Closure|array|null
     */
    protected $control_delete = null;

    /**
     * @var \Closure|array|null
     */
    protected $control_force_delete = null;

    /**
     * @var \Closure|array|null
     */
    protected $control_restore = null;

    /**
     * @var \Closure|array|null
     */
    protected $control_selectable = null;

    /**
     * @var bool
     */
    protected $checks = true;

    /**
     * @param  \Closure|array|mixed  $test
     * @return $this
     */
    public function controls($test = null)
    {
        $this->set_test_var('controls', $test);

        return $this;
    }

    /**
     * @param  \Closure|array|mixed  $test
     * @return $this
     */
    public function controlInfo($test = null)
    {
        $this->set_test_var('control_info', $test);

        return $this;
    }

    /**
     * @param  \Closure|array|mixed  $test
     * @return $this
     */
    public function controlEdit($test = null)
    {
        $this->set_test_var('control_edit', $test);

        return $this;
    }

    /**
     * @param  \Closure|array|mixed  $test
     * @return $this
     */
    public function controlDelete($test = null)
    {
        $this->set_test_var('control_delete', $test);

        return $this;
    }

    /**
     * @param  \Closure|array|mixed  $test
     * @return $this
     */
    public function controlForceDelete($test = null)
    {
        $this->set_test_var('control_force_delete', $test);

        return $this;
    }

    /**
     * @param  \Closure|array|mixed  $test
     * @return $this
     */
    public function controlRestore($test = null)
    {
        $this->set_test_var('control_restore', $test);

        return $this;
    }

    /**
     * @param  \Closure|array|mixed  $test
     * @return $this
     */
    public function controlSelect($test = null)
    {
        $this->set_test_var('control_selectable', $test);

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
    
    protected $action = [];

    /**
     * @param $jax
     * @param $title
     * @param  null  $icon
     * @param  null  $confirm
     * @return $this
     */
    public function action($jax, $title, $icon = null, $confirm = null)
    {
        $this->action[] = [
            'jax' => $jax,
            'title' => $title,
            'icon' => $icon,
            'confirm' => $confirm
        ];

        return $this;
    }

    /**
     * Create default controls
     */
    protected function _create_controls()
    {
        if ($this->get_test_var('controls')) {

            if ($this->checks && !request()->has('show_deleted')) {
                $this->to_prepend()->column(function (SPAN $span) {
                    $span->_addClass('fit');
                    $span->view('lte::segment.model_table_checkbox', [
                        'id' => false,
                        'table_id' => $this->model_name,
                        'object' => $this->model_class,
                        'actions' => $this->action,
                        'columns' => collect($this->columns)->filter(function ($i) { return isset($i['field']) && is_string($i['field']); })->pluck('field')->toArray()
                    ])->render();
                }, function (Model $model) {

                    return view('lte::segment.model_table_checkbox', [
                        'id' => $model->id,
                        'table_id' => $this->model_name,
                        'disabled' => !$this->get_test_var('control_selectable', [$model])
                    ])->render();

                }, null, true);
            }

            if (request()->has('show_deleted')) {

                $this->deleted_at();
            }

            $this->column(function (SPAN $span) {
                $span->_addClass('fit');
            }, function (Model $model) {
                return ButtonGroup::create(function (ButtonGroup $group) use ($model) {

                    $menu = gets()->lte->menu->now;

                    if ($menu) {

                        $key = $model->getRouteKey();

                        if (!request()->has('show_deleted')) {
                            if (isset($menu['link.edit']) && $this->get_test_var('control_edit', [$model]) && lte_controller_can('edit')) {
                                $group->resourceEdit($menu['link.edit']($key), '');
                            }

                            if (isset($menu['link.destroy']) && $this->get_test_var('control_delete', [$model]) && lte_controller_can('destroy')) {
                                $group->resourceDestroy($menu['link.destroy']($key), '', $model->getRouteKeyName(), $key);
                            }

                            if (isset($menu['link.show']) && $this->get_test_var('control_info', [$model]) && lte_controller_can('show')) {
                                $group->resourceInfo($menu['link.show']($key), '');
                            }
                        } else {

                            if (isset($menu['link.destroy']) && $this->get_test_var('control_restore', [$model]) && lte_controller_can('restore')) {
                                $group->resourceRestore($menu['link.destroy']($key), '', $model->getRouteKeyName(), $key);
                            }

                            if (isset($menu['link.destroy']) && $this->get_test_var('control_force_delete', [$model]) && lte_controller_can('force_destroy')) {
                                $group->resourceForceDestroy($menu['link.destroy']($key), '', $model->getRouteKeyName(), $key);
                            }
                        }
                    }
                });
            });
        }
    }

    /**
     * @param  string  $var_name
     * @param $test
     */
    protected function set_test_var(string $var_name, $test)
    {
        if (is_embedded_call($test)) {

            $this->{$var_name} = $test;
        }

        else {

            $this->{$var_name} = function () use ($test) { return !!$test; };
        }
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
}