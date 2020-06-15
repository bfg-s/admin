<?php

namespace Lar\LteAdmin\Segments\Tagable\Cores;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use Lar\Developer\Core\Traits\Stateable;
use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\TD;
use Lar\Layout\Tags\TH;
use Lar\LteAdmin\Segments\Tagable\ButtonGroup;
use Lar\Tagable\Events\onRender;

/**
 * Class Table
 * @package Lar\LteAdmin\Components
 */
class CoreModelTable extends DIV implements onRender
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
     * @var \Closure
     */
    protected $controls;

    /**
     * @var \Closure
     */
    protected $info_control;

    /**
     * @var \Closure
     */
    protected $delete_control;

    /**
     * @var \Closure
     */
    protected $edit_control;

    /**
     * @var bool
     */
    protected $default_id = true;

    /**
     * @var bool
     */
    protected $default_fields = true;

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
        $this->controls =
        $this->info_control =
        $this->delete_control =
        $this->edit_control = function () { return true; };

        if (is_array($model)) {

            $instructions = $model;
            $model = null;
        }

        if (!$model) {

            $model = gets()->lte->menu->model;
        }

        $this->model = $instructions ? eloquent_instruction($model, $instructions) : $model;

        parent::__construct();

        $this->save_table_requests();

        $this->when($params);
    }

    /**
     * Save last table request for returns
     */
    protected function save_table_requests()
    {
        $all = request()->query();
        unset($all['_pjax']);
        session(['temp_lte_table_data' => $all]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     */
    public static function getLastRequest()
    {
        return session()->pull('temp_lte_table_data', []);
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
     * @param  string|null  $sort_field
     * @param  bool  $prepend
     * @param  bool  $th
     * @return \Lar\Layout\Tags\TD
     */
    public function column($title, $field = null, $sort_field = null, bool $prepend = false, bool $th = false)
    {
        if (!$this->table) {

            $this->makeTable();
        }

        if (!$field) { $field = \Str::slug($title, '_'); }

        $title = $this->sorterOnColumn($title, is_string($field) ? $field : "", $sort_field);

        $column = $this->table->column($title, $field, $prepend, $th);

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
        $this->table = \Lar\Layout\Tags\TABLE::create(['table', 'table-sm', 'table-hover']);

        $this->appEnd($this->table);

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
    protected function sorterOnColumn($title, string $field, $sort_field = null)
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
        return $this->column(__('lte.created_at'), 'true_data:created_at', 'created_at');
    }

    /**
     * Create updated_at field
     *
     * @return TD
     */
    public function updated_at()
    {
        return $this->column(__('lte.updated_at'), 'true_data:updated_at', 'updated_at');
    }

    /**
     * Create deleted_at field
     *
     * @return TD
     */
    public function deleted_at()
    {
        return $this->column(__('lte.deleted_at'), 'true_data:deleted_at', 'deleted_at');
    }

    /**
     * @param  \Closure|null  $test
     * @return $this
     */
    public function disableControls(\Closure $test = null)
    {
        $this->controls = $test ? $test : function () { return false; };

        return $this;
    }

    /**
     * @return $this
     */
    public function disableInfo(\Closure $test = null)
    {
        $this->info_control = $test ? $test : function () { return false; };

        return $this;
    }

    /**
     * @param  \Closure|null  $test
     * @return $this
     */
    public function disableEdit(\Closure $test = null)
    {
        $this->edit_control = $test ? $test : function () { return false; };

        return $this;
    }

    /**
     * @param  \Closure|null  $test
     * @return $this
     */
    public function disableDelete(\Closure $test = null)
    {
        $this->delete_control = $test ? $test : function () { return false; };

        return $this;
    }

    /**
     * @return $this
     */
    public function disableDefaults()
    {
        $this->default_fields = false;

        return $this;
    }

    /**
     * @param  bool  $bool
     * @return $this
     */
    public function disableDefaultsId(bool $bool = true)
    {
        $this->default_id = !$bool;

        return $this;
    }

    /**
     * Function execute on render component
     *
     * @return mixed
     */
    public function onRender()
    {
        if ($this->default_fields) {

            if ($this->default_id) {

                $this->column(function (TH $th) {
                    //$th->addClass('fit');

                    return __('lte.id');
                }, 'id', true, true, true);
            }


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

                    if (($this->controls)($model) && $menu) {
                        $key = $model->getRouteKey();

                        if (($this->edit_control)($model)) {
                            $group->resourceEdit($menu['link.edit']($key), '');
                        }

                        if (($this->delete_control)($model)) {
                            $group->resourceDestroy($menu['link.destroy']($key), '', $model->getRouteKeyName(), $key);
                        }

                        if (($this->info_control)($model)) {
                            $group->resourceInfo($menu['link.show']($key), '');
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
        else if ($this->model) $count = $this->model->count();

        if (!$count) {

            $this->table->getTbody()
                ->tr()
                ->td(['colspan' => $this->table->columnCount()])
                ->div(['alert alert-warning mt-3 text-center text-justify', 'role' => 'alert', 'style' => 'background: rgba(255, 193, 7, 0.1); text-transform: uppercase;'])
                ->text(__('lte.empty'));
        }
    }
}