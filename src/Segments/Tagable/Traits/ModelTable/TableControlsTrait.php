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
     * @var \Closure|null
     */
    protected $controls = null;

    /**
     * @var \Closure|null
     */
    protected $control_info = null;

    /**
     * @var \Closure|null
     */
    protected $control_edit = null;

    /**
     * @var \Closure|null
     */
    protected $control_delete = null;

    /**
     * @var \Closure|null
     */
    protected $control_selectable = null;

    /**
     * @var bool
     */
    protected $checks = true;

    /**
     * @param  \Closure|mixed  $test
     * @return $this
     */
    public function controls($test = null)
    {
        $this->set_test_var('controls', $test);

        return $this;
    }

    /**
     * @param  \Closure|mixed  $test
     * @return $this
     */
    public function controlInfo($test = null)
    {
        $this->set_test_var('control_info', $test);

        return $this;
    }

    /**
     * @param  \Closure|mixed  $test
     * @return $this
     */
    public function controlEdit($test = null)
    {
        $this->set_test_var('control_edit', $test);

        return $this;
    }

    /**
     * @param  \Closure|mixed  $test
     * @return $this
     */
    public function controlDelete($test = null)
    {
        $this->set_test_var('control_delete', $test);

        return $this;
    }

    /**
     * @param  \Closure|mixed  $test
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

    /**
     * Create default controls
     */
    protected function _create_controls()
    {
        if ($this->get_test_var('controls')) {

            if ($this->checks) {
                $this->to_prepend()->column(function (SPAN $span) {
                    $span->_addClass('fit');
                    $span->view('lte::segment.model_table_checkbox', [
                        'id' => false,
                        'table_id' => $this->model_name,
                        'object' => $this->model_class
                    ])->render();
                }, function (Model $model) {

                    return view('lte::segment.model_table_checkbox', [
                        'id' => $model->id,
                        'table_id' => $this->model_name,
                        'disabled' => !$this->get_test_var('control_selectable', [$model])
                    ])->render();

                }, null, true);
            }

            $this->column(function (SPAN $span) {
                $span->_addClass('fit');
            }, function (Model $model) {
                return ButtonGroup::create(function (ButtonGroup $group) use ($model) {

                    $menu = gets()->lte->menu->now;

                    if ($menu) {

                        $key = $model->getRouteKey();

                        if ($this->get_test_var('control_edit', [$model])) {
                            $group->resourceEdit($menu['link.edit']($key), '');
                        }

                        if ($this->get_test_var('control_delete', [$model])) {
                            $group->resourceDestroy($menu['link.destroy']($key), '', $model->getRouteKeyName(), $key);
                        }

                        if ($this->get_test_var('control_info', [$model])) {
                            $group->resourceInfo($menu['link.show']($key), '');
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
        if ($test instanceof \Closure) {

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

            return ($this->{$var_name})(...$args);
        }

        return true;
    }
}