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

            if ((request()->has('show_deleted') && !$column['trash']) || $column['hide']) {

                continue;
            }

            $this->makeHeadTH($header, $column, $key);

            $header_count++;
        }

        if (request()->has('q') && request()->ajax() && !request()->pjax() && $this->search && $this->search->fieldsCount()) {

            die($this->paginate->toJson());
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

            if ((request()->has('show_deleted') && !$column['trash']) || $column['hide']) {

                continue;
            }

            $td = $tr->td();

            if (is_string($value)) {
                $value = multi_dot_call($item, $value);
            } else if (is_embedded_call($value)) {
                $value = call_user_func_array($value, [
                    $item, $column['label'], $td, $column['header'], $tr
                ]);
//                $value = embedded_call($value, [
//                    'model' => $item,
//                    'value' => $value,
//                    'field' => $column['field'],
//                    'sort' => $column['sort'],
//                    'title' => $column['label'],
//                    TD::class => $td,
//                    TR::class => $tr,
//                    TH::class => $column['header'],
//                    (is_object($item) ? get_class($item) : gettype($item)) => $item,
//                ]);
            }
            foreach ($column['macros'] as $macro) {
                $value = static::callE($macro[0], [
                    $value, $macro[1], $item, $column['field'], $column['label'], $td, $column['header'], $tr
                ]);
//                $value = static::callExtension($macro[0], [
//                    'model' => $item,
//                    'value' => $value,
//                    'field' => $column['field'],
//                    'sort' => $column['sort'],
//                    'title' => $column['label'],
//                    'props' => $macro[1],
//                    TD::class => $td,
//                    TR::class => $tr,
//                    TH::class => $column['header'],
//                    (is_object($item) ? get_class($item) : gettype($item)) => $item,
//                ]);
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
                    $select = request()->get($this->model_name . '_type', $this->order_type);
                    $now = request()->get($this->model_name, $this->order_field) == $column['sort'];
                    $type = $now ? ($select === 'desc' ? 'down': 'up-alt') : 'down';
                    $th->a()->setHref(urlWithGet([
                        $this->model_name => $column['sort'],
                        $this->model_name . "_type" => $now ? ($select === 'desc' ? 'asc' : 'desc') : 'asc'
                    ]))->i(["fas fa-sort-amount-{$type} d-none d-sm-inline"], ':space')
                        ->_span($column['label'])
                        ->addClassIf(!$now, 'text-body');
                }
                else {
                    $th->span()->when([$column['label']]);
                }
                if ($column['info']) {

                    $th->text(':space')->i(['title' => __($column['info'])])->icon_info_circle();
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

        $select_type = request()->get($this->model_name . '_type', $this->order_type);
        $this->order_field = request()->get($this->model_name, $this->order_field);

        if ($this->model instanceof Relation || $this->model instanceof Builder || $this->model instanceof Model) {

            $this->model = static::fire_pipes($this->model, $this->model_class);

            foreach ($this->model_control as $item) {
                if ($item instanceof SearchForm) {
                    $this->model = $item->makeModel($this->model);
                } else if (is_embedded_call($item)) {
                    $r = call_user_func($item, $this->model);
                    if ($r) $this->model = $r;
                } else if (is_array($item)) {
                    $this->model = eloquent_instruction($this->model, $item);
                }
            }

            return $this->paginate = $this->model->orderBy($this->order_field, $select_type)->paginate($this->per_page, ['*'], $this->model_name . "_page");
        }

        else if ($this->model instanceof \Illuminate\Support\Collection) {

            if (request()->has($this->model_name)) {
                $model = $this->model
                    ->{strtolower($select_type) == "asc" ? "sortBy" : "sortByDesc"}($this->order_field);
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
