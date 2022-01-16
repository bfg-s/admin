<?php

namespace Lar\LteAdmin\Controllers\Traits;

use Lar\Layout\Respond;
use Lar\LteAdmin\Segments\LtePage;

trait DefaultControllerResourceMethodsTrait
{
    /**
     * Display a listing of the resource.
     * @param LtePage $page
     * @return LtePage
     */
    public function index_default(LtePage $page)
    {
        return $page
            ->card()
            ->search()
            ->table();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return LtePage
     */
    public function create_default() {

        return method_exists($this, 'matrix') ? app()->call([$this, 'matrix']) : app()->call([$this, 'matrix_default']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return LtePage
     */
    public function edit_default() {

        return method_exists($this, 'matrix') ? app()->call([$this, 'matrix']) : app()->call([$this, 'matrix_default']);
    }

    /**
     * Display the specified resource.
     * @param LtePage $page
     * @return LtePage
     */
    public function show_default(LtePage $page)
    {
        return $page
            ->card()
            ->info();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  array|null  $data
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_default(array $data = null) {

        if (method_exists($this, 'edit')) {
            embedded_call([$this, 'edit']);
        } else {
            embedded_call([$this, 'edit_default']);
        }

        $save = $data ?? request()->all();

        $save = static::fire_pipes($save, 'save');

        if ($back = back_validate($save, static::$rules, static::$rule_messages)) {

            return $back;
        }

        $updated = $this->requestToModel($save);

        if ($updated) {

            static::fire_pipes($updated, 'updated');

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
            embedded_call([$this, 'create']);
        } else {
            embedded_call([$this, 'create_default']);
        }

        $save = $data ?? request()->all();

        $save = static::fire_pipes($save, 'save');

        if ($back = back_validate($save, static::$rules, static::$rule_messages)) {

            return $back;
        }

        $stored = $this->requestToModel($save);

        if ($stored) {

            static::fire_pipes($stored, 'stored');

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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|Respond
     * @throws \Exception
     */
    public function destroy_default() {

        $model = $this->existsModel();

        $force = request()->has('force') && request()->get('force');

        $restore = request()->has('restore') && request()->get('restore');

        if ($force || $restore) {

            $model = $this->model()->onlyTrashed()->where($this->model()->getRouteKeyName(), $this->model_primary());
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

    public function matrix_default(LtePage $page)
    {
        return $page
            ->card()
            ->form();
    }
}
