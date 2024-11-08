<?php

declare(strict_types=1);

namespace Admin\Traits;

use Admin\Delegates\Card;
use Admin\Delegates\Form;
use Admin\Delegates\ModelInfoTable;
use Admin\Delegates\ModelTable;
use Admin\Delegates\SearchForm;
use Admin\Facades\Admin;
use Admin\Page;
use Admin\Respond;
use App;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * Trait with standard controller methods for the admin panel.
 */
trait DefaultControllerResourceMethodsTrait
{
    /**
     * Index method for displaying a list of records and filtering them.
     *
     * @param  Page  $page
     * @param  Card  $card
     * @param  SearchForm  $searchForm
     * @param  ModelTable  $modelTable
     * @return Page
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function index_default(Page $page, Card $card, SearchForm $searchForm, ModelTable $modelTable): Page
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
     * Form method for displaying the form for editing and adding a record.
     *
     * @param  Page  $page
     * @param  Card  $card
     * @param  Form  $form
     * @return Page
     */
    public function matrix_default(Page $page, Card $card, Form $form): Page
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
     * Display method for displaying information about a record.
     *
     * @param  Page  $page
     * @param  Card  $card
     * @param  ModelInfoTable  $modelInfoTable
     * @return Page
     */
    public function show_default(Page $page, Card $card, ModelInfoTable $modelInfoTable): Page
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
     *
     * @return mixed
     */
    public function create_default(): mixed
    {
        return method_exists($this, 'matrix') ? app()->call([$this, 'matrix']) : app()->call([$this, 'matrix_default']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return mixed
     */
    public function edit_default(): mixed
    {
        return method_exists($this, 'matrix') ? app()->call([$this, 'matrix']) : app()->call([$this, 'matrix_default']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  array|null  $data
     * @return bool|Application|RedirectResponse|Redirector|Respond|JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function update_default(array $data = null): Respond|bool|Redirector|RedirectResponse|Application|JsonResponse
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

        if ($back = back_validate($save, static::$rules, static::$ruleMessages)) {
            return $back;
        }

        $updated = $this->requestToModel($save, static::$imageModifiers);

        if ($updated) {
            admin_log_success('Updated successfully',
                trim(get_class($this->model()).' for '.$this->model()->getRouteKeyName().': '.$this->model()->{$this->model()->getRouteKeyName()},
                    '\\'), 'fas fa-save');
            Respond::glob()->put('alert::success', __('admin.saved_successfully'));

            Admin::important('model', $this->model(), static::$resource);

            if (Admin::isApiMode()) {

                return response()->json([
                    'status' => 'success',
                    'message' => __('admin.saved_successfully'),
                ]);
            }
        } else {
            admin_log_danger('Update error',
                trim(get_class($this->model()).' for '.$this->model()->getRouteKeyName().': '.$this->model()->{$this->model()->getRouteKeyName()},
                    '\\'), 'fas fa-save');
            Respond::glob()->put('alert::error', __('admin.unknown_error'));

            if (Admin::isApiMode()) {

                return response()->json([
                    'status' => 'error',
                    'message' => __('admin.unknown_error'),
                ]);
            }
        }

        return $this->returnTo();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  array|null  $data
     * @return bool|Application|RedirectResponse|Redirector|Respond|JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function store_default(array $data = null): Respond|bool|Redirector|RedirectResponse|Application|JsonResponse
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

        if ($back = back_validate($save, static::$rules, static::$ruleMessages)) {

            return $back;
        }

        $stored = $this->requestToModel($save, static::$imageModifiers);

        if ($stored) {
            Respond::glob()->put('alert::success', __('admin.saved_successfully'));
            admin_log_success('Create successfully', trim(get_class($this->model()), '\\'), 'fas fa-save');
            Respond::glob()->put('alert::success', __('admin.successfully_created'));

            if (Admin::isApiMode()) {

                Admin::important('model', $stored, static::$resource);

                return response()->json([
                    'status' => 'success',
                    'message' => __('admin.successfully_created'),
                ]);
            }
        } else {
            admin_log_danger('Create error', trim(get_class($this->model()), '\\'), 'fas fa-save');
            Respond::glob()->put('alert::error', __('admin.unknown_error'));

            if (Admin::isApiMode()) {

                return response()->json([
                    'status' => 'error',
                    'message' => __('admin.unknown_error'),
                ]);
            }
        }

        return $this->returnTo();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Application|RedirectResponse|Redirector|Respond|JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function destroy_default(): Respond|Redirector|RedirectResponse|Application|JsonResponse
    {
        $model = $this->existsModel();

        $force = request()->has('force') && request()->get('force');

        $restore = request()->has('restore') && request()->get('restore');

        Admin::expectedQuery('force');
        Admin::expectedQuery('restore');

        if ($force || $restore) {
            $model = $this->model()->onlyTrashed()->where($this->model()->getRouteKeyName(), $this->model_primary());
        }

        if ($model) {
            $modelName = trim(get_class($this->model()), '\\');
            $key = $this->model_primary();

            try {
                if ($restore && $model->restore()) {
                    admin_log_warning('Successfully restored',
                        $modelName.' for '.$this->model()->getRouteKeyName().': '.$key, 'fas fa-trash-restore-alt');
                    Respond::glob()->put('alert::success', __('admin.successfully_restored'));
                    if (Admin::isApiMode()) {
                        return response()->json([
                            'status' => 'success',
                            'message' => __('admin.successfully_restored'),
                        ]);
                    }
                } elseif ($force && $model->forceDelete()) {
                    admin_log_danger('Successfully force deleted',
                        $modelName.' for '.$this->model()->getRouteKeyName().': '.$key, 'fas fa-eraser');
                    Respond::glob()->put('alert::success', __('admin.successfully_deleted'));
                    if (Admin::isApiMode()) {
                        return response()->json([
                            'status' => 'success',
                            'message' => __('admin.successfully_deleted'),
                        ]);
                    }
                } elseif ($model->delete()) {
                    admin_log_danger('Successfully deleted',
                        $modelName.' for '.$this->model()->getRouteKeyName().': '.$key, 'fas fa-trash');
                    Respond::glob()->put('alert::success', __('admin.successfully_deleted'));
                    if (Admin::isApiMode()) {
                        return response()->json([
                            'status' => 'success',
                            'message' => __('admin.successfully_deleted'),
                        ]);
                    }
                } else {
                    Respond::glob()->put('alert::error', __('admin.unknown_error'));
                    if (Admin::isApiMode()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => __('admin.unknown_error'),
                        ]);
                    }
                }
            } catch (Exception $exception) {
                if (!App::isLocal()) {
                    Respond::glob()->put('alert::error', __('admin.unknown_error'));
                    if (Admin::isApiMode()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => __('admin.unknown_error'),
                        ]);
                    }
                } else {
                    Respond::glob()->put('alert::error', $exception->getMessage());
                    if (Admin::isApiMode()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => $exception->getMessage(),
                        ]);
                    }
                }
            }
        } else {
            Respond::glob()->put('alert::error', __('admin.model_not_found'));
            if (Admin::isApiMode()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('admin.model_not_found'),
                ]);
            }
        }

        return request('_after', 'index') == 'index'
            ? Respond::glob()->put('location', admin_repo()->now->getLinkIndex())
            : Respond::glob()->reload();
    }
}
