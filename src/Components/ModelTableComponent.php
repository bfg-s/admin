<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Middlewares\DomMiddleware;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Admin\Traits\Delegable;
use Admin\Traits\ModelTable\TableBuilderTrait;
use Admin\Traits\ModelTable\TableControlsTrait;
use Admin\Traits\ModelTable\TableExtensionTrait;
use Admin\Traits\ModelTable\TableHelpersTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @methods static::$extensions (...$params) static
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
    use Delegable;

    /**
     * @var SearchFormComponent|null
     */
    public ?SearchFormComponent $search = null;

    /**
     * @var string
     */
    protected string $view = 'model-table';

    /**
     * @var mixed|null
     */
    protected mixed $label = null;

    /**
     * @var bool
     */
    protected bool $hasHidden = false;

    /**
     * @var Model|Builder|Relation|Collection|array|null
     */
    protected $model;

    /**
     * @var LengthAwarePaginator|null
     */
    protected ?LengthAwarePaginator $paginate = null;

    /**
     * @var mixed|array
     */
    protected mixed $model_control = [];

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

    /**
     * @var bool
     */
    public static bool $is_export = false;

    /**
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        $this->per_page = config('admin.model-table-component.per_page', $this->per_page);
        $this->per_pages = config('admin.model-table-component.per_pages', $this->per_pages);
        $this->order_field = config('admin.model-table-component.order_field', $this->order_field);
        $this->order_type = config('admin.model-table-component.order_type', $this->order_type);

        parent::__construct();

        if (request()->has($this->model_name)) {
            try {
                $this->order_field = request()->get($this->model_name);
            } catch (\Throwable) {}
        }

        if (request()->has($this->model_name.'_type')) {
            try {
                $type = request()->get($this->model_name.'_type');
            } catch (\Throwable) {}
            $this->order_type = $type === 'asc' || $type === 'desc' ? $type : 'asc';
        }

        $this->delegatesNow($delegates);

        $this->_create_controls();

        try {
            if (request()->has($this->model_name.'_per_page') && in_array(
                    request()->get($this->model_name.'_per_page'),
                    $this->per_pages
                )) {
                $this->per_page = (string) request()->get($this->model_name.'_per_page');
            }
        } catch (\Throwable) {}

        DomMiddleware::setModelTableComponent($this);
    }

    /**
     * @return LengthAwarePaginator|null
     */
    public function getPaginate(): ?LengthAwarePaginator
    {
        return $this->paginate;
    }

    /**
     * @return string[]
     */
    protected function viewData(): array
    {
        return [
            'id' => $this->model_name,
        ];
    }

    /**
     * @param  null  $model
     * @return static
     */
    public function model($model = null): static
    {
        if ($model instanceof SearchFormComponent) {
            $this->search = $model;
            $this->model_control[] = $model;
            $model = null;
        }

        if ($model) {

            parent::model($model);
        }

        return $this;
    }

    /**
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function createModel(): mixed
    {
        if (is_array($this->model)) {
            $this->model = collect($this->model);
        }

        if (request()->has('show_deleted')) {
            $this->model = $this->model->onlyTrashed();
        }

        $select_type = request()->get($this->model_name.'_type', $this->order_type);
        $this->order_field = request()->get($this->model_name, $this->order_field);

        if ($this->model instanceof Relation || $this->model instanceof Builder || $this->model instanceof Model) {
            foreach ($this->model_control as $item) {
                if ($item instanceof SearchFormComponent) {
                    $this->model = $item->makeModel($this->model);
                } elseif (is_embedded_call($item)) {
                    $r = call_user_func($item, $this->model);
                    if ($r) {
                        $this->model = $r;
                    }
                } elseif (is_array($item)) {
                    $this->model = eloquent_instruction($this->model, $item);
                }
            }

            return $this->paginate = $this->model->orderBy($this->order_field, $select_type)->paginate(
                $this->per_page,
                ['*'],
                $this->model_name.'_page'
            );
        } elseif ($this->model instanceof Collection) {
            if (request()->has($this->model_name)) {
                $model = $this->model
                    ->{strtolower($select_type) == 'asc' ? 'sortBy' : 'sortByDesc'}($this->order_field);
            } else {
                $model = $this->model;
            }

            return $this->paginate = $model->paginate($this->per_page, $this->model_name.'_page');
        }

        return $this->model;
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|string
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (
            preg_match("/^col_(.+)$/", $name, $matches)
            && !isset(Component::$inputs[$name])
            && !Component::hasComponentStatic($name)
        ) {
            $name = str_replace(['_dot_', '__'], '.', Str::snake($matches[1], '_'));
            $label = $arguments[0] ?? ucfirst(str_replace(['.', '_'], ' ', $name));

            return $this->col(Lang::has("admin.$label") ? __("admin.$label") : $label, $name);
        } else {
            if (
                preg_match("/^sort_in_(.+)$/", $name, $m)
                && !isset(Component::$inputs[$name])
                && !Component::hasComponentStatic($name)
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

    /**
     * @param  string  $name
     * @return ModelTableComponent
     */
    public function __get(string $name)
    {
        if (
            preg_match("/^col_(.+)$/", $name, $matches)
            && !isset(Component::$inputs[$name])
            && !Component::hasComponentStatic($name)
        ) {
            $name = str_replace(['_dot_', '__'], '.', Str::snake($matches[1], '_'));
            $label = ucfirst(str_replace(['.', '_'], ' ', $name));

            return $this->col(Lang::has("admin.$name") ? __("admin.$name") : $label, $name);
        } else {
            if (
                preg_match("/^sort_in_(.+)$/", $name, $m)
                && !isset(Component::$inputs[$name])
                && !Component::hasComponentStatic($name)
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

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function mount(): void
    {
        $this->createModel();

        $this->_build();
    }
}
