<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\Developer\Core\Traits\Piplineble;
use Lar\LteAdmin\Segments\Tagable\Traits\ModelTable\TableExtensionTrait;
use Lar\LteAdmin\Segments\Tagable\Traits\ModelTable\TableControlsTrait;
use Lar\LteAdmin\Segments\Tagable\Traits\ModelTable\TableBuilderTrait;
use Lar\LteAdmin\Segments\Tagable\Traits\ModelTable\TableHelpersTrait;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Abstracts\Component;
use Illuminate\Support\Collection;

/**
 * Class ModelTable
 * @package Lar\LteAdmin\Segments\Tagable
 * @methods static::$extensions (...$params)
 * @mixin ModelTableMacroList
 * @mixin ModelTableMethods
 */
class ModelTable extends Component {

    use TableHelpersTrait,
        TableExtensionTrait,
        TableBuilderTrait,
        TableControlsTrait,
        Macroable,
        Piplineble,
        Delegable;

    /**
     * @var string
     */
    protected $element = "table";
    protected $label = null;
    protected $hasHidden = false;

    /**
     * @var string[]
     */
    protected $props = [
        'table', 'table-sm', 'table-hover'
    ];

    /**
     * @var Model|Builder|Relation|Collection|array|null
     */
    protected $model;

    /**
     * @var LengthAwarePaginator
     */
    protected $paginate;

    /**
     * @var \Closure|array|null
     */
    protected $model_control = [];

    /**
     * @var string
     */
    protected $model_name;

    /**
     * @var string
     */
    protected $model_class;

    /**
     * @var int
     */
    protected $per_page = 15;

    /**
     * @var int[]
     */
    protected $per_pages = [10, 15, 20, 50, 100];

    /**
     * @var string
     */
    protected $order_field = 'id';

    /**
     * @var string
     */
    protected $order_type = 'desc';

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var string|null
     */
    protected $last;

    /**
     * @var bool
     */
    protected $prepend = false;

    /**
     * @var SearchForm
     */
    public $search;

    /**
     * Table2 constructor.
     * @param  \Closure|Model|Builder|Relation|Collection|array|null  $model
     * @param  mixed  ...$params
     * @throws \ReflectionException
     */
    public function __construct($model = null, ...$params)
    {
        parent::__construct();

        if (is_embedded_call($model)) {

            $params[] = $model;
            $model = null;
        }

        if (!$model) {
            $this->model = gets()->lte->menu->model;
        } else {
            $this->model = is_string($model) ? new $model() : $model;
        }

        $this->model_name = $this->getModelName();

        if (request()->has($this->model_name)) {

            $this->order_field = request()->get($this->model_name);
        }

        if (request()->has($this->model_name . "_type")) {

            $type = request()->get($this->model_name . "_type");
            $this->order_type = $type === 'asc' || $type === 'desc' ? $type : 'asc';
        }

        $this->when($params);

        $this->callConstructEvents();

        $this->toExecute("_create_controls", "_build");

        $this->save_table_requests();
    }

    public static function toContainer(DIV $div, $arguments)
    {
        if ($div instanceof Card) {

            return $div->bodyModelTable(...$arguments);
        }

        return $div;
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
     * @param $name
     * @param $arguments
     * @return bool|\Lar\Tagable\Tag|string
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if (static::hasExtension($name) && isset($this->columns[$this->last])) {

            $this->columns[$this->last]['macros'][] = [$name, $arguments];

            return $this;
        }

        return parent::__call($name, $arguments);
    }
}
