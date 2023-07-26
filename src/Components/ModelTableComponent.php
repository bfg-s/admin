<?php

namespace Admin\Components;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Lar\Tagable\Tag;
use Admin\Controllers\Controller;
use Admin\Traits\Delegable;
use Admin\Traits\Macroable;
use Admin\Traits\ModelTable\TableBuilderTrait;
use Admin\Traits\ModelTable\TableControlsTrait;
use Admin\Traits\ModelTable\TableExtensionTrait;
use Admin\Traits\ModelTable\TableHelpersTrait;
use Admin\Traits\Piplineble;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @methods static::$extensions (...$params) static
 * @mixin ModelTableComponentMacroList
 * @mixin ModelTableComponentFields
 * @mixin ModelTableComponentMethods
 * @property-read ModelTableComponent $sort
 */
class ModelTableComponent extends Component
{
    use TableHelpersTrait;
    use TableExtensionTrait;
    use TableBuilderTrait;
    use TableControlsTrait;
    use Macroable;
    use Piplineble;
    use Delegable;

    /**
     * @var SearchFormComponent
     */
    public $search;
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
     * @var Closure|array|null
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
    protected $per_pages = [10, 15, 20, 50, 100, 500, 1000];
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
    protected $controlsObj;

    public static bool $is_export = false;

    /**
     * @param ...$delegates
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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

        $this->_create_controls();
    }

    /**
     * @param  SearchFormComponent|Closure|array|Builder|Relation  $instruction
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
     * @param $name
     * @param $arguments
     * @return bool|Tag|string
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (
            preg_match("/^col_(.+)$/", $name, $matches)
            && !isset(Component::$inputs[$name])
            && !Controller::hasExplanation($name)
        ) {
            $name = str_replace(['_dot_', '__'], '.', Str::snake($matches[1], '_'));
            $label = $arguments[0] ?? ucfirst(str_replace(['.', '_'], ' ', $name));

            return $this->col(Lang::has("admin.$label") ? __("admin.$label") : $label, $name);
        } else {
            if (
                preg_match("/^sort_in_(.+)$/", $name, $m)
                && !isset(Component::$inputs[$name])
                && !Controller::hasExplanation($name)
            ) {
                return $this->sort($m[1]);
            } else {
                if (static::hasExtension($name) && isset($this->columns[$this->last])) {
                    $this->columns[$this->last]['macros'][] = [$name, $arguments];

                    return $this;
                }
            }
        }

        return parent::__call($name, $arguments);
    }

    public function __get(string $name)
    {
        if (
            preg_match("/^col_(.+)$/", $name, $matches)
            && !isset(Component::$inputs[$name])
            && !Controller::hasExplanation($name)
        ) {
            $name = str_replace(['_dot_', '__'], '.', Str::snake($matches[1], '_'));
            $label = ucfirst(str_replace(['.', '_'], ' ', $name));

            return $this->col(Lang::has("admin.$name") ? __("admin.$name") : $label, $name);
        } else {
            if (
                preg_match("/^sort_in_(.+)$/", $name, $m)
                && !isset(Component::$inputs[$name])
                && !Controller::hasExplanation($name)
            ) {
                return $this->sort($m[1]);
            } else {
                if (method_exists($this, $name)) {
                    return $this->{$name}();
                }
            }
        }

        return parent::__get($name);
    }

    protected function mount()
    {
        $this->_build();
    }
}
