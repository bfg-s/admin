<?php

namespace LteAdmin\Controllers\Traits;

use App;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Lar\Layout\Respond;
use LteAdmin\Delegates\Card;
use LteAdmin\Delegates\Form;
use LteAdmin\Delegates\ModelInfoTable;
use LteAdmin\Delegates\ModelTable;
use LteAdmin\Delegates\SearchForm;
use LteAdmin\Page;
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
            embedded_call([$this, 'edit']);
        } else {
            embedded_call([$this, 'edit_default']);
        }

        $save = $data ?? request()->all();

        if ($back = back_validate($save, static::$rules, static::$rule_messages)) {
            return $back;
        }

        $updated = $this->requestToModel($save);

        if ($updated) {
            respond()->put('alert::success', __('lte.saved_successfully'));
        } else {
            respond()->put('alert::error', __('lte.unknown_error'));
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
            embedded_call([$this, 'create']);
        } else {
            embedded_call([$this, 'create_default']);
        }

        $save = $data ?? request()->all();

        if ($back = back_validate($save, static::$rules, static::$rule_messages)) {
            return $back;
        }

        $stored = $this->requestToModel($save);

        if ($stored) {
            respond()->put('alert::success', __('lte.successfully_created'));
        } else {
            respond()->put('alert::error', __('lte.unknown_error'));
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

        $model = static::fire_pipes($model, 'delete');

        if ($model) {
            try {
                if ($restore && $model->restore()) {
                    respond()->put('alert::success', __('lte.successfully_restored'));

                    respond()->reload();
                } elseif ($force && $model->forceDelete()) {
                    respond()->put('alert::success', __('lte.successfully_deleted'));

                    respond()->reload();
                } elseif ($model->delete()) {
                    respond()->put('alert::success', __('lte.successfully_deleted'));

                    respond()->reload();
                } else {
                    respond()->put('alert::error', __('lte.unknown_error'));
                }
            } catch (Exception $exception) {
                if (!App::isLocal()) {
                    respond()->put('alert::error', __('lte.unknown_error'));
                } else {
                    respond()->put('alert::error', $exception->getMessage());
                }
            }
        } else {
            respond()->put('alert::error', __('lte.model_not_found'));
        }

        return request('_after', 'index') == 'index'
            ? respond()->put('ljs.$nav.goTo', admin_repo()->now['link.index']())
            : respond()->reload();
    }
}
