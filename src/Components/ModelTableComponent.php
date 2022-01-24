<?php

namespace Lar\LteAdmin\Components;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Lar\Developer\Core\Traits\Piplineble;
use Lar\LteAdmin\Components\Traits\ModelTable\TableBuilderTrait;
use Lar\LteAdmin\Components\Traits\ModelTable\TableControlsTrait;
use Lar\LteAdmin\Components\Traits\ModelTable\TableExtensionTrait;
use Lar\LteAdmin\Components\Traits\ModelTable\TableHelpersTrait;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;

/**
 * @methods static::$extensions (...$params) static
 * @mixin ModelTableComponentMacroList
 * @mixin ModelTableComponentMethods
 */
class ModelTableComponent extends Component
{
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
    protected $element = 'table';
    protected $label = null;
    protected $hasHidden = false;

    /**
     * @var string[]
     */
    protected $props = [
        'table', 'table-sm', 'table-hover',
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
     * @var SearchFormComponent
     */
    public $search;

    /**
     * @param ...$delegates
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(...$delegates)
    {
        parent::__construct();

        if (request()->has($this->model_name)) {
            $this->order_field = request()->get($this->model_name);
        }

        if (request()->has($this->model_name.'_type')) {
            $type = request()->get($this->model_name.'_type');
            $this->order_type = $type === 'asc' || $type === 'desc' ? $type : 'asc';
        }

        $this->delegatesNow($delegates);

        $this->save_table_requests();
    }

    /**
     * @param SearchFormComponent|\Closure|array|Builder|Relation $instruction
     * @return $this|static
     */
    public function model($model = null)
    {
        if ($model instanceof SearchFormComponent) {
            $this->search = $model;
            $this->model_control[] = $model;
            $model = null;
        }

        return parent::model($model);
    }

    /**
     * Save last table request for returns.
     */
    protected function save_table_requests()
    {
    }

    protected function mount()
    {
        $this->_create_controls();
        $this->_build();
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
