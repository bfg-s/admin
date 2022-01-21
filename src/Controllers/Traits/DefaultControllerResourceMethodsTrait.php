<?php

namespace Lar\LteAdmin\Controllers\Traits;

use Lar\Layout\Respond;
use Lar\LteAdmin\Page;

trait DefaultControllerResourceMethodsTrait
{
    /**
     * @param  Page  $page
     * @return \Lar\LteAdmin\Components\Contents\CardContent|\Lar\LteAdmin\Components\ModelTableComponent|\Lar\LteAdmin\Components\SearchFormComponent|Page|\Lar\LteAdmin\PageMethods
     */
    public function index_default(Page $page)
    {
        return $page
            ->card()
            ->search_form()
            ->model_table();
    }

    /**
     * @param  Page  $page
     * @return \Lar\LteAdmin\Components\Contents\CardContent|\Lar\LteAdmin\Components\FormComponent|Page|\Lar\LteAdmin\PageMethods
     */
    public function matrix_default(Page $page)
    {
        return $page
            ->card()
            ->form();
    }

    /**
     * Display the specified resource.
     * @param  Page  $page
     * @return \Lar\LteAdmin\Components\Contents\CardContent|\Lar\LteAdmin\Components\ModelInfoTableComponent|Page|\Lar\LteAdmin\PageMethods
     */
    public function show_default(Page $page)
    {
        return $page
            ->card()
            ->model_info_table();
    }

    /**
     * Show the form for creating a new resource.
     * @return mixed
     */
    public function create_default()
    {
        return method_exists($this, 'matrix') ? app()->call([$this, 'matrix']) : app()->call([$this, 'matrix_default']);
    }

    /**
     * Show the form for editing the specified resource.
     * @return mixed
     */
    public function edit_default()
    {
        return method_exists($this, 'matrix') ? app()->call([$this, 'matrix']) : app()->call([$this, 'matrix_default']);
    }

    /**
     * Update the specified resource in storage.
     * @param  array|null  $data
     * @return bool|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|Respond
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Throwable
     */
    public function update_default(array $data = null)
    {
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
        } else {
            respond()->toast_error(__('lte.unknown_error'));
        }

        return $this->returnTo();
    }

    /**
     * Store a newly created resource in storage.
     * @param  array|null  $data
     * @return bool|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|Respond
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Throwable
     */
    public function store_default(array $data = null)
    {
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
        } else {
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
    public function destroy_default()
    {
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
                } elseif ($force && $model->forceDelete()) {
                    respond()->toast_success(__('lte.successfully_deleted'));

                    respond()->reload();
                } elseif ($model->delete()) {
                    respond()->toast_success(__('lte.successfully_deleted'));

                    respond()->reload();
                } else {
                    respond()->toast_error(__('lte.unknown_error'));
                }
            } catch (\Exception $exception) {
                if (! \App::isLocal()) {
                    respond()->toast_error(__('lte.unknown_error'));
                } else {
                    respond()->toast_error($exception->getMessage());
                }
            }
        } else {
            respond()->toast_error(__('lte.model_not_found'));
        }

        return respond();
    }
}
