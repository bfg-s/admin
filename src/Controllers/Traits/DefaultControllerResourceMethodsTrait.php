<?php

namespace Admin\Controllers\Traits;

use Admin\Repositories\AdminRepository;
use App;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Admin\Respond;
use Admin\Delegates\Card;
use Admin\Delegates\Form;
use Admin\Delegates\ModelInfoTable;
use Admin\Delegates\ModelTable;
use Admin\Delegates\SearchForm;
use Admin\Page;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

trait DefaultControllerResourceMethodsTrait
{
    /**
     * @param  Page  $page
     * @param  Card  $card
     * @param  SearchForm  $searchForm
     * @param  ModelTable  $modelTable
     * @return Page
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index_default(Page $page, Card $card, SearchForm $searchForm, ModelTable $modelTable)
    {
        return $page->card(
            $card->search_form(
                $searchForm->id(),
                $searchForm->at(),
            ),
            $card->model_table(
                $modelTable->id(),
                $modelTable->at(),
            )
        );
    }

    /**
     * @param  Page  $page
     * @param  Card  $card
     * @param  Form  $form
     * @return Page
     */
    public function matrix_default(Page $page, Card $card, Form $form)
    {
        return $page->card(
            $card->form(
                $form->ifEdit()->info_id(),
                $form->ifEdit()->info_updated_at(),
                $form->ifEdit()->info_created_at(),
            ),
            $card->footer_form(),
        );
    }

    /**
     * @param  Page  $page
     * @param  Card  $card
     * @param  ModelInfoTable  $modelInfoTable
     * @return Page
     */
    public function show_default(Page $page, Card $card, ModelInfoTable $modelInfoTable)
    {
        return $page->card(
            $card->model_info_table(
                $modelInfoTable->id(),
                $modelInfoTable->at(),
            )
        );
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
     * @return bool|Application|RedirectResponse|Redirector|Respond
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function update_default(array $data = null)
    {
        if (method_exists($this, 'edit')) {
            $result = embedded_call([$this, 'edit']);
        } else {
            $result = embedded_call([$this, 'edit_default']);
        }

        while ($result instanceof Renderable) {
            $result = $result->render();
        }

        $save = $data ?? request()->all();

        if ($back = back_validate($save, static::$rules, static::$rule_messages)) {
            return $back;
        }

        $updated = $this->requestToModel($save, static::$imageModifiers);

        if ($updated) {
            admin_log_success('Updated successfully', trim(get_class($this->model()) . ' for ' . $this->model()->getRouteKeyName() . ': ' . $this->model()->{$this->model()->getRouteKeyName()}, '\\'), 'fas fa-save');
            Respond::glob()->put('alert::success', __('admin.saved_successfully'));
        } else {
            admin_log_danger('Update error', trim(get_class($this->model()) . ' for ' . $this->model()->getRouteKeyName() . ': ' . $this->model()->{$this->model()->getRouteKeyName()}, '\\'), 'fas fa-save');
            Respond::glob()->put('alert::error', __('admin.unknown_error'));
        }

        return $this->returnTo();
    }

    /**
     * Store a newly created resource in storage.
     * @param  array|null  $data
     * @return bool|Application|RedirectResponse|Redirector|Respond
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function store_default(array $data = null)
    {
        if (method_exists($this, 'create')) {
            $result = embedded_call([$this, 'create']);
        } else {
            $result = embedded_call([$this, 'create_default']);
        }

        while ($result instanceof Renderable) {
            $result = $result->render();
        }

        $save = $data ?? request()->all();

        if ($back = back_validate($save, static::$rules, static::$rule_messages)) {
            return $back;
        }

        $stored = $this->requestToModel($save, static::$imageModifiers);

        if ($stored) {

            Respond::glob()->put('alert::success', __('admin.saved_successfully'));
            admin_log_success('Create successfully', trim(get_class($this->model()), '\\'), 'fas fa-save');
            Respond::glob()->put('alert::success', __('admin.successfully_created'));
        } else {
            admin_log_danger('Create error', trim(get_class($this->model()), '\\'), 'fas fa-save');
            Respond::glob()->put('alert::error', __('admin.unknown_error'));
        }

        return $this->returnTo();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Application|RedirectResponse|Redirector|Respond
     * @throws Exception
     */
    public function destroy_default()
    {
        $model = $this->existsModel();

        $force = request()->has('force') && request()->get('force');

        $restore = request()->has('restore') && request()->get('restore');

        if ($force || $restore) {
            $model = $this->model()->onlyTrashed()->where($this->model()->getRouteKeyName(), $this->model_primary());
        }

        if ($model) {

            $modelName = trim(get_class($this->model()), '\\');
            $key = $this->model_primary();

            try {
                if ($restore && $model->restore()) {
                    admin_log_warning('Successfully restored', $modelName . ' for ' . $this->model()->getRouteKeyName() . ': ' . $key, 'fas fa-trash-restore-alt');
                    Respond::glob()->put('alert::success', __('admin.successfully_restored'));

                } elseif ($force && $model->forceDelete()) {
                    admin_log_danger('Successfully force deleted', $modelName . ' for ' . $this->model()->getRouteKeyName() . ': ' . $key, 'fas fa-eraser');
                    Respond::glob()->put('alert::success', __('admin.successfully_deleted'));
                } elseif ($model->delete()) {
                    admin_log_danger('Successfully deleted', $modelName . ' for ' . $this->model()->getRouteKeyName() . ': ' . $key, 'fas fa-trash');
                    Respond::glob()->put('alert::success', __('admin.successfully_deleted'));
                } else {
                    Respond::glob()->put('alert::error', __('admin.unknown_error'));
                }
            } catch (Exception $exception) {
                if (!App::isLocal()) {
                    Respond::glob()->put('alert::error', __('admin.unknown_error'));
                } else {
                    Respond::glob()->put('alert::error', $exception->getMessage());
                }
            }
        } else {
            Respond::glob()->put('alert::error', __('admin.model_not_found'));
        }

        return request('_after', 'index') == 'index'
            ? Respond::glob()->put('location', admin_repo()->now->getLinkIndex())
            : Respond::glob()->reload();
    }
}
