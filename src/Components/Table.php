<?php

namespace Lar\LteAdmin\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use Lar\Developer\Core\Traits\Stateable;
use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\TD;
use Lar\Layout\Tags\TH;
use Lar\LteAdmin\Components\ButtonGroup;
use Lar\Tagable\Events\onRender;

/**
 * Class Table
 * @package Lar\LteAdmin\Components
 */
class Table extends DIV implements onRender
{
    use Stateable;

    /**
     * @var bool
     */
    protected $only_content = true;

    /**
     * @var array
     */
    protected $state = [
        'per_page' => 10,
        'per_pages' => [10,20,50,100]
    ];

    /**
     * @var array
     */
    protected $apply = [
        //'makeTable'
    ];

    /**
     * @var \Lar\Layout\Tags\TABLE
     */
    protected $table;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var LengthAwarePaginator
     */
    protected $paginate;

    /**
     * Shoe default controls
     *
     * @var bool
     */
    protected $controls = true;

    /**
     * @var bool
     */
    protected $info_control = true;

    /**
     * @var bool
     */
    protected $delete_control = true;

    /**
     * @var bool
     */
    protected $edit_control = true;

    /**
     * @var string
     */
    private $order_by_field = "id";

    /**
     * @var string
     */
    private $order_by_type = "asc";

    /**
     * Table constructor.
     * @param  Model  $model
     * @param  array  $instructions
     * @param  mixed  ...$params
     */
    public function __construct($model = null, array $instructions = null, ...$params)
    {
        if (is_array($model)) {

            $instructions = $model;
            $model = null;
        }

        if (!$model) {

            $model = gets()->lte->menu->model;
        }

        $this->model = $instructions ? eloquent_instruction($model, $instructions) : $model;

        parent::__construct();

        $this->when($params);
    }

    /**
     * @param  int  $per_page
     * @return $this
     */
    public function perPage($per_page)
    {
        $this->per_page = $per_page;

        return $this;
    }

    /**
     * @param  array  $per_pages
     * @return $this
     */
    public function perPages($per_pages)
    {
        if (is_string($per_pages)) {

            $per_pages = explode('|', $per_pages);
        }

        $this->per_pages = $per_pages;

        $this->perPage($per_pages[0]);

        return $this;
    }

    /**
     * @param $configure
     * @return $this
     */
    public function model($configure)
    {
        if (is_array($configure)) {

            $this->instruction($configure);
        }

        else if ($configure instanceof \Closure) {

            $this->model = $configure($this->model);
        }

        return $this;
    }

    /**
     * @param  array  $instruction
     * @return $this
     */
    public function instruction(array $instruction)
    {
        $this->model = eloquent_instruction($this->model, $instruction);

        return $this;
    }

    /**
     * @param  string|null  $field
     * @return $this
     */
    public function orderDesc(string $field = null)
    {
        $this->order_by_type = 'desc';

        if ($field) {

            $this->order_by_field = $field;
        }

        return $this;
    }

    /**
     * @param  string|null  $field
     * @param  string|null  $order
     * @return $this
     */
    public function orderBy(string $field = null, string $order = null)
    {
        if ($field) {

            $this->order_by_field = $field;
        }

        if ($order) {

            $this->order_by_type = $order;
        }

        return $this;
    }

    /**
     * Column setter
     *
     * @param $title
     * @param $field
     * @param string|null $sort_field
     * @param bool $prepend
     * @return \Lar\Layout\Tags\TD
     */
    public function column($title, $field = null, $sort_field = null, bool $prepend = false)
    {
        if (!$this->table) {

            $this->makeTable();
        }

        if (!$field) { $field = \Str::slug($title, '_'); }

        $title = $this->sorterOnColumn($title, is_string($field) ? $field : "", $sort_field);

        $column = $this->table->column($title, $field, $prepend);

        if ($column['td']->ext) {

            return $column['td']->tr_field;
        }

        else {

            return $column['td'];
        }
    }

    /**
     * @param  \Closure  $closure
     * @return $this
     */
    public function controlPrepend(\Closure $closure)
    {
        return $this->addEvent('controls_prepend', $closure);
    }

    /**
     * @param  \Closure  $closure
     * @return $this
     */
    public function controlAppend(\Closure $closure)
    {
        return $this->addEvent('controls_append', $closure);
    }

    /*********************/

    /**
     * @return $this
     */
    public function makeTable()
    {
        $this->table = $this->table(['table', 'table-sm']);

        $this->table->rowsOnPage($this->requestState('per_page'));

        if (is_string($this->model)) {

            $this->model = new $this->model;
        }

        $this->paginate = $this->table->setModel($this->model, $this->order_by_field, $this->order_by_type)->model;

        return $this;
    }

    /**
     * @param $title
     * @param string $field
     * @param null $sort_field
     * @return \Closure|mixed
     */
    public function sorterOnColumn($title, string $field, $sort_field = null)
    {
        if ($sort_field) {

            if ($sort_field === "*") { $sort_field = $field; }

            if ($sort_field === true) { $sort_field = $field; }

            return function (TH $th) use ($sort_field, $title) {

                $id = $this->table->getId();

                if ($this->table->getOrderField() == $sort_field) {

                    $type = strtolower($this->table->getOrderType());

                    $i = $type !== "asc" ? "up" : "down";

                    $th->prepEnd()->a(["fas fa-chevron-circle-{$i}"])
                        ->setHref(urlWithGet([$id => $sort_field, "type" => ($type == "asc" ? "desc" : "asc")]));
                }

                else {

                    $th->prepEnd()->a(["fas fa-chevron-circle-down"])
                        ->setHref(urlWithGet([$id => $sort_field, "type" => "asc"]));
                }

                $th->text(':space');

                if ($title instanceof \Closure) {

                    return custom_closure_call($title, [
                        TH::class => $th
                    ]);
                }

                else {

                    return $title;
                }
            };
        }

        return $title;
    }

    /**
     * @return \Lar\Layout\Abstracts\Component
     */
    public function footer()
    {
        return DIV::create(['card-footer'])->view('lte::widget.model_table.footer', [
            'model' => $this->model,
            'paginator' => $this->paginate,
            'from' => (($this->paginate->currentPage() * $this->paginate->perPage()) - $this->paginate->perPage()) + 1,
            'to' => ($this->paginate->currentPage() * $this->paginate->perPage()) > $this->paginate->total() ? $this->paginate->total() : ($this->paginate->currentPage() * $this->paginate->perPage()),
            'per_page' => $this->requestState('per_page'),
            'state' => $this->state,
            'elements' => $this->paginationElements($this->paginate)
        ]);
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

    /**
     * Create created_at field
     *
     * @return TD
     */
    public function created_at()
    {
        return $this->column(__('lte::admin.created_at'), 'true_data:created_at', true);
    }

    /**
     * Create updated_at field
     *
     * @return TD
     */
    public function updated_at()
    {
        return $this->column(__('lte::admin.updated_at'), 'true_data:updated_at', true);
    }

    /**
     * Create deleted_at field
     *
     * @return TD
     */
    public function deleted_at()
    {
        return $this->column(__('lte::admin.deleted_at'), 'true_data:deleted_at', true);
    }

    /**
     * @return $this
     */
    public function disableControls()
    {
        $this->controls = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableInfo()
    {
        $this->info_control = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableEdit()
    {
        $this->edit_control = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableDelete()
    {
        $this->delete_control = false;

        return $this;
    }

    /**
     * Function execute on render component
     *
     * @return mixed
     */
    public function onRender()
    {
        if ($this->controls) {

            $this->column(function (TH $th) {

                $th->addClass('fit');

                return __('lte::admin.id');

            }, 'id', true, true);


            $this->column(function (TH $th) {

                $th->addClass('fit');

                return view('lte::widget.model_table.checkbox', [
                    'id' => false,
                    'table_id' => $this->table->getId()
                ])->render();

            }, function (Model $model) {

                return view('lte::widget.model_table.checkbox', [
                    'id' => $model->id,
                    'table_id' => $this->table->getId()
                ])->render();

            }, null, true);

            $this->column(function (TH $th) {

                $th->addClass('fit');

                return '';

            }, function (TD $td, Model $model) {

                $td->appEnd(ButtonGroup::create(function (ButtonGroup $group) use ($model, $td) {

                    $menu = gets()->lte->menu->now;

                    $this->callEvent('controls_prepend', [
                        ButtonGroup::class => $group,
                        get_class($model) => $model,
                        Model::class => $model,
                        TD::class => $td
                    ]);

                    if ($menu) {

                        $action = \Str::before(\Route::currentRouteAction(), '@');

                        $rk_name = $model->getRouteKeyName();

                        $key = $model->getOriginal($rk_name);

                        if ($this->edit_control && $key && isset($menu['link.edit']) && (method_exists($action, 'edit') || method_exists($action, 'edit_default'))) {

                            $group->success('fas fa-edit')->setTitle(__('lte::admin.edit'))->dataClick()->location(
                                $menu['link.edit']([$menu['model.param'] => $key])
                            );
                        }

                        if ($this->delete_control && $key && isset($menu['link.destroy']) && (method_exists($action, 'destroy') || method_exists($action, 'destroy_default'))) {

                            $group->danger('fas fa-trash-alt')
                                ->setTitle(__('lte::admin.delete'))->setDatas([
                                    'click' => 'alert::confirm',
                                    'params' => [
                                        __('lte::admin.delete_subject', ['subject' => strtoupper($rk_name).":{$key}?"]),
                                        $menu['link.destroy']([$menu['model.param'] => $key]) . " >> \$jax.del"
                                    ]
                                ]);
                        }

                        if ($this->info_control && $key && isset($menu['link.show']) && (method_exists($action, 'show') || method_exists($action, 'show_default'))) {

                            $group->info('fas fa-info-circle')->setTitle(__('lte::admin.information'))->dataClick()->location(
                                $menu['link.show']([$menu['model.param'] => $key])
                            );
                        }
                    }

                    $this->callEvent('controls_append', [
                        ButtonGroup::class => $group,
                        get_class($model) => $model,
                        Model::class => $model,
                        TD::class => $td
                    ]);
                }));
            });
        }

        $count = 0;

        if (is_array($this->model)) $count = count($this->model);
        else $count = $this->model->count();

        if (!$count) {

            $this->table->getTbody()
                ->tr()
                ->td(['colspan' => $this->table->columnCount()])
                ->div(['alert alert-warning mt-3 text-center text-justify', 'role' => 'alert', 'style' => 'background: rgba(255, 193, 7, 0.1); text-transform: uppercase;'])
                ->text(__('lte::admin.empty'));
        }
    }
}