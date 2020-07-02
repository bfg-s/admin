<?php

namespace Lar\LteAdmin\Segments\Tagable\Traits\ModelTable;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\UrlWindow;
use Lar\Layout\Tags\TD;
use Lar\Layout\Tags\TH;
use Lar\Layout\Tags\TR;
use Lar\LteAdmin\Segments\Tagable\SearchForm;

/**
 * Trait TableBuilderTrait
 * @package Lar\LteAdmin\Segments\Tagable\Traits
 */
trait TableBuilderTrait {

    /**
     * @throws \ReflectionException
     */
    protected function _build()
    {
        $this->callRenderEvents();

        $this->setId($this->model_name);

        if (request()->has($this->model_name . '_per_page') && in_array(request()->get($this->model_name . '_per_page'), $this->per_pages)) {

            $this->per_page = (string)request()->get($this->model_name . '_per_page');
        }

        $this->createModel();

        $header = $this->thead()->tr();

        $header_count = 0;

        foreach ($this->columns as $key => $column) {

            if (request()->has('show_deleted') && !$column['trash']) {

                continue;
            }

            $this->makeHeadTH($header, $column, $key);

            $header_count++;
        }

        $body = $this->tbody();

        foreach ($this->paginate ?? $this->model as $item) {

            $this->makeBodyTR($body->tr(), $item);
        }

        $count = 0;

        if (is_array($this->model)) $count = count($this->model);
        else if ($this->paginate) $count = $this->paginate->count();

        if (!$count) {

            $body->tr()
                ->td(['colspan' => $header_count])
                ->div(['alert alert-warning mt-3 text-center text-justify', 'role' => 'alert', 'style' => 'background: rgba(255, 193, 7, 0.1); text-transform: uppercase;'])
                ->text(__('lte.empty'));
        }
    }

    /**
     * @param  TR  $tr
     * @param $item
     * @throws \ReflectionException
     */
    protected function makeBodyTR(TR $tr, $item)
    {
        foreach ($this->columns as $column) {

            $value = $column['field'];

            if (request()->has('show_deleted') && !$column['trash']) {

                continue;
            }

            $td = $tr->td();

            if (is_string($value)) {
                $value = multi_dot_call($item, $value);
            } else if (is_array($value) || $value instanceof \Closure) {
                $value = custom_closure_call($value, [
                    'model' => $item,
                    'value' => $value,
                    'field' => $column['field'],
                    'sort' => $column['sort'],
                    'title' => $column['label'],
                    TD::class => $td,
                    TR::class => $tr,
                    TH::class => $column['header'],
                    (is_object($item) ? get_class($item) : gettype($item)) => $item,
                ]);
            }
            foreach ($column['macros'] as $macro) {
                $value = static::callExtension($macro[0], [
                    'model' => $item,
                    'value' => $value,
                    'field' => $column['field'],
                    'sort' => $column['sort'],
                    'title' => $column['label'],
                    'props' => $macro[1],
                    TD::class => $td,
                    TR::class => $tr,
                    TH::class => $column['header'],
                    (is_object($item) ? get_class($item) : gettype($item)) => $item,
                ]);
            }

            $td->when($value);
        }
    }

    /**
     * @param  TR  $tr
     * @param  array  $column
     * @param  string  $key
     */
    protected function makeHeadTH(TR $tr, array $column, string $key)
    {
        $this->columns[$key]['header'] = $tr->th(['scope' => 'col'])
            ->when(function (TH $th) use ($column) {
                if (is_string($column['sort'])) {
                    $now = request()->get($this->model_name, $this->order_field) == $column['sort'];
                    $type = $now ? ($this->order_type === 'desc' ? 'up-alt' : 'down') : 'down';
                    $th->a()->setHref(urlWithGet([
                        $this->model_name => $column['sort'],
                        $this->model_name . "_type" => $now ? ($this->order_type === 'desc' ? 'asc' : 'desc') : 'asc'
                    ]))->i(["fas fa-sort-amount-{$type} d-none d-sm-inline"], ':space')
                        ->_span($column['label'])
                        ->addClassIf(!$now, 'text-body');
                }
                else {
                    $th->span()->when([$column['label']]);
                }
            });
    }

    /**
     * @return array|\Closure|\Illuminate\Contracts\Pagination\LengthAwarePaginator|Model|Relation|\Lar\LteAdmin\Getters\Menu|string|null
     */
    protected function createModel()
    {
        if (is_array($this->model)) { $this->model = collect($this->model); }

        if (request()->has('show_deleted')) {
            $this->model = $this->model->onlyTrashed();
        }

        if ($this->model instanceof Relation || $this->model instanceof Builder || $this->model instanceof Model) {

            foreach ($this->model_control as $item) {
                if ($item instanceof SearchForm) {
                    $this->model = $item->makeModel($this->model);
                } else if ($item instanceof \Closure) {
                    ($item)($this->model);
                } else if (is_array($item)) {
                    $this->model = eloquent_instruction($this->model, $item);
                }
            }

            return $this->paginate = $this->model->orderBy($this->order_field, $this->order_type)->paginate($this->per_page, ['*'], $this->model_name . "_page");
        }

        else if ($this->model instanceof \Illuminate\Support\Collection) {

            if (request()->has($this->model_name)) {
                $model = $this->model
                    ->{strtolower($this->order_type) == "asc" ? "sortBy" : "sortByDesc"}($this->order_field);
            } else {
                $model = $this->model;
            }

            return $this->paginate = $model->paginate($this->per_page, $this->model_name . "_page");
        }

        return $this->model;
    }

    /**
     * Get the array of elements to pass to the view.
     *
     * @param LengthAwarePaginator $page
     * @return array
     */
    protected function paginationElements(LengthAwarePaginator $page)
    {
        $window = UrlWindow::make($page);

        return array_filter([
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
    }
}