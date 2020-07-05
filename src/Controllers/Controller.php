<?php

namespace Lar\LteAdmin\Controllers;

use Lar\Layout\Respond;
use Lar\LteAdmin\Core\Traits\Piplineble;
use Lar\LteAdmin\Segments\Info;
use Lar\LteAdmin\Segments\Matrix;
use Lar\LteAdmin\Segments\Sheet;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;
use Lar\LteAdmin\Segments\Tagable\ModelTable;

/**
 * Class Controller
 *
 * @package Lar\LteAdmin\Controllers
 */
class Controller extends BaseController
{
    use Piplineble;

    /**
     * Permission functions for methods
     *
     * @var array
     */
    static $permission_functions = [];

    /**
     * @var array
     */
    public static $rules = [];

    /**
     * @var array
     */
    public static $rule_messages = [];

    /**
     * @var array
     */
    public static $crypt_fields = [];

    /**
     * Display a listing of the resource.
     *
     * @return Sheet
     */
    public function index_default() {

        return Sheet::create(function (ModelTable $table) {
            $table->id();
            $table->at();
        });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Matrix
     */
    public function create_default() {

        if (method_exists($this, 'matrix')) {

            return $this->matrix();
        }

        return new Matrix(function (Form $form) {
            $form->autoMake();
        });
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Matrix
     */
    public function edit_default() {

        if (method_exists($this, 'matrix')) {

            return $this->matrix();
        }

        return new Matrix(function (Form $form) {
            $form->autoMake();
        });
    }

    /**
     * Display the specified resource.
     *
     * @return Info
     */
    public function show_default() {

        return Info::create(function (ModelInfoTable $table) {
            $table->id();
            $table->at();
        });
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  array|null  $data
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_default(array $data = null) {

        if (method_exists($this, 'edit')) {
            custom_closure_call([$this, 'edit']);
        } else {
            custom_closure_call([$this, 'edit_default']);
        }

        $save = $data ?? request()->all();

        if ($back = back_validate($save, static::$rules, static::$rule_messages)) {

            return $back;
        }

        $save = static::fire_pipes($save, 'save');
        
        if ($this->requestToModel($save)) {

            respond()->toast_success(__('lte.saved_successfully'));
        }

        else {

            respond()->toast_error(__('lte.unknown_error'));
        }

        return $this->returnTo();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  array|null  $data
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store_default(array $data = null) {

        if (method_exists($this, 'create')) {
            custom_closure_call([$this, 'create']);
        } else {
            custom_closure_call([$this, 'create_default']);
        }

        $save = $data ?? request()->all();

        if ($back = back_validate($save, static::$rules, static::$rule_messages)) {

            return $back;
        }

        $save = static::fire_pipes($save, 'save');

        if ($this->requestToModel($save)) {

            respond()->toast_success(__('lte.successfully_created'));
        }

        else {

            respond()->toast_error(__('lte.unknown_error'));
        }

        return $this->returnTo();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Respond
     * @throws \Exception
     */
    public function destroy_default() {

        $model = $this->existsModel();

        $force = request()->has('force') && request()->get('force');

        $restore = request()->has('restore') && request()->get('restore');

        if ($force || $restore) {

            $model = $this->model()->onlyTrashed()->find($this->model_primary());
        }

        $model = static::fire_pipes($model, 'delete');

        if ($model) {

            try {

                if ($restore && $model->restore()) {

                    respond()->toast_success(__('lte.successfully_restored'));

                    respond()->reload();
                }
                else if ($force && $model->forceDelete()) {

                    respond()->toast_success(__('lte.successfully_deleted'));

                    respond()->reload();
                }
                else if ($model->delete()) {

                    respond()->toast_success(__('lte.successfully_deleted'));

                    respond()->reload();
                }

                else {

                    respond()->toast_error(__('lte.unknown_error'));
                }
            } catch (\Exception $exception) {

                if (!\App::isLocal()) {
                    respond()->toast_error(__('lte.unknown_error'));
                } else {
                    respond()->toast_error($exception->getMessage());
                }
            }

        }

        else {

            respond()->toast_error(__('lte.model_not_found'));
        }

        return respond();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|Respond
     */
    public function returnTo()
    {
        if (request()->ajax() && !request()->pjax()) {

            return respond()->reload();
        }

        $_after = request()->get('_after', 'index');

        if ($_after === 'index' && $menu = gets()->lte->menu->now) {

            $last = session()->pull('temp_lte_table_data', []);

            return \redirect($menu['link'] . (count($last) ? '?'.http_build_query($last) : ''))->with('_after', $_after);
        }

        return back()->with('_after', $_after);
    }

    /**
     * Trap for default methods
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $method_default = "{$method}_default";

        if (method_exists($this, $method_default)) {
            
            return $this->{$method_default}();
        }

        parent::__call($method, $parameters);
    }
}
