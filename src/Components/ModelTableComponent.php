<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Components\ModelTable\BodyComponent;
use Admin\Components\ModelTable\ColumnComponent;
use Admin\Components\ModelTable\HeadComponent;
use Admin\Components\ModelTable\HeaderComponent;
use Admin\Components\ModelTable\RowComponent;
use Admin\Core\ModelTableAction;
use Admin\Core\PrepareExport;
use Admin\Facades\Admin;
use Admin\Middlewares\DomMiddleware;
use Admin\Models\AdminPermission;
use Admin\Traits\Resouceable;
use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * Model table component of the admin panel.
 *
 * @methods static::$extensions (...$params) static
 * @mixin ModelTableComponentFields
 * @mixin ModelTableComponentMethods
 * @property-read ModelTableComponent $sort
 */
class ModelTableComponent extends Component
{
    use Resouceable;

    /**
     * Search form component.
     *
     * @var SearchFormComponent|null
     */
    public SearchFormComponent|null $search = null;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'model-table';

    /**
     * Indicates whether the show and hide column buttons are enabled in actions.
     *
     * @var bool
     */
    protected bool $hasHidden = false;

    /**
     * Current model paginator.
     *
     * @var LengthAwarePaginator|null
     */
    protected LengthAwarePaginator|null $paginate = null;

    /**
     * Model modifiers such as search form.
     *
     * @var mixed|array
     */
    protected mixed $model_control = [];

    /**
     * Number of rows per page.
     *
     * @var int
     */
    protected int $per_page = 15;

    /**
     * Possible number of rows that can be displayed on one page.
     *
     * @var int[]
     */
    protected array $per_pages = [10, 15, 20, 50, 100, 500, 1000];

    /**
     * The field by which rows are sorted.
     *
     * @var string
     */
    protected string $order_field = 'id';

    /**
     * The type by which rows are sorted.
     *
     * @var string
     */
    protected string $order_type = 'desc';

    /**
     * All columns of the model table.
     *
     * @var array
     */
    protected array $columns = [];

    /**
     * Last column index.
     *
     * @var string|null
     */
    protected string|null $last;

    /**
     * Add a column to the beginning of a list of columns.
     *
     * @var bool
     */
    protected bool $prepend = false;

    /**
     * Has models on process.
     *
     * @var array
     */
    protected static array $models = [];

    /**
     * List of table column extensions.
     *
     * @var array
     */
    public static array $extensions = [];

    /**
     * Property for checking whether all control buttons are displayed.
     *
     * @var Closure|array|null
     */
    protected Closure|array|null $controls = null;

    /**
     * Property for checking whether the info button is displayed.
     *
     * @var Closure|array|null
     */
    protected Closure|array|null $control_info = null;

    /**
     * Property for checking whether the edit button is displayed.
     *
     * @var Closure|array|null
     */
    protected Closure|array|null $control_edit = null;

    /**
     * Property for checking whether the delete button is displayed.
     *
     * @var Closure|array|null
     */
    protected Closure|array|null $control_delete = null;

    /**
     * Property for checking whether the force delete button is displayed.
     *
     * @var Closure|array|null
     */
    protected Closure|array|null $control_force_delete = null;

    /**
     * Property for checking whether the restore button is displayed.
     *
     * @var Closure|array|null
     */
    protected Closure|array|null $control_restore = null;

    /**
     * Property for checking whether the selection checkbox is displayed.
     *
     * @var Closure|array|null
     */
    protected Closure|array|null $control_selectable = null;

    /**
     * Property for enabling or disabling table row selection checkboxes.
     *
     * @var bool
     */
    protected bool $checks = true;

    /**
     * Property for enabling or disabling the deletion of table rows selected by checkboxes.
     *
     * @var Closure|array|bool|null
     */
    protected Closure|array|bool|null $check_delete = null;

    /**
     * Actions with table rows highlighted by checkboxes.
     *
     * @var array
     */
    protected array $action = [];

    /**
     * Realtime marker, if enabled, the component will be updated at the specified frequency.
     *
     * @var bool
     */
    protected bool $realTime = true;

    /**
     * ModelTableComponent constructor.
     *
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
            } catch (Throwable) {
            }
        }

        if (request()->has($this->model_name.'_type')) {
            try {
                $type = request()->get($this->model_name.'_type');
            } catch (Throwable) {
            }
            $this->order_type = $type === 'asc' || $type === 'desc' ? $type : 'asc';
        }

        $this->delegatesNow($delegates);

        $this->createControls();

        try {
            if (request()->has($this->model_name.'_per_page')) {
                $this->per_page = (int) request()->get($this->model_name.'_per_page');
            }
        } catch (Throwable) {
        }

        DomMiddleware::setModelTableComponent($this);
    }

    /**
     * Get the current pagination class of the table model with cards.
     *
     * @return LengthAwarePaginator|null
     */
    public function getPaginate(): ?LengthAwarePaginator
    {
        return $this->paginate;
    }

    /**
     * Set a model or search form for the model card components.
     *
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
     * Add extension class for model table.
     *
     * @param  string  $class
     */
    public static function addExtensionClass(string $class): void
    {
        if (class_exists($class)) {
            $class = new $class();

            foreach (get_class_methods($class) as $method) {
                static::addExtension($method, $class);
            }
        }
    }

    /**
     * Add macro for model table.
     *
     * @param  string  $name
     * @param  Closure|object  $object
     * @param  string|null  $method
     */
    public static function addExtension(string $name, $object, string $method = null): void
    {
        if (is_embedded_call($object)) {
            static::$extensions[$name] = $object;
        } else {
            if (!$method) {
                $method = $name;
            }
            static::$extensions[$name] = [$object, $method];
        }
    }

    /**
     * Call extension of model table.
     *
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed|null
     * @throws Throwable
     */
    public static function callExtension(string $name, array $arguments): mixed
    {
        if (static::hasExtension($name)) {
            return embedded_call(static::$extensions[$name], $arguments);
        }

        return null;
    }

    /**
     * Check has model table extension.
     *
     * @param  string  $name
     * @return bool
     */
    public static function hasExtension(string $name): bool
    {
        return isset(static::$extensions[$name]);
    }

    /**
     * Alias for call extension of model table.
     *
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed|null
     */
    public static function callE(string $name, array $arguments): mixed
    {
        if (static::hasExtension($name)) {
            return call_user_func_array(static::$extensions[$name], $arguments);
        }

        return null;
    }

    /**
     * Get model table extension list.
     *
     * @return array
     */
    public static function getExtensionList(): array
    {
        return static::$extensions;
    }

    /**
     * Set an arbitrary number of posts per page.
     *
     * @param  array  $per_pages
     * @return $this
     */
    public function perPages(array $per_pages): static
    {
        $this->per_pages = $per_pages;

        return $this;
    }

    /**
     * Set the field to be sorted and the type.
     *
     * @param  string  $field
     * @param  string  $type
     * @return $this
     */
    public function orderBy(string $field, string $type = 'asc'): static
    {
        $this->order_field = $field;

        $this->order_type = $type;

        return $this;
    }

    /**
     * Add a new button group.
     *
     * @param ...$delegates
     * @return $this
     */
    public function buttons(...$delegates): static
    {
        $title = isset($delegates[0]) && is_string($delegates[0])
            ? $delegates[0]
            : null;

        $this->col(
            $title,
            fn($model) => ButtonsComponent::create()
                ->model($model)
                ->delegatesNow($delegates)
        );

        return $this;
    }

    /**
     * Alias for add a new column to the model table.
     *
     * @param  array|string|Closure|null  $label
     * @param  array|string|Closure|null  $field
     * @return $this
     */
    public function col(array|string|Closure|null $label, array|string|Closure $field = null): static
    {
        return $this->column($label, $field);
    }

    /**
     * Add a new column to the model table.
     *
     * @param  string|Closure|array|null  $label
     * @param  array|string|Closure|null  $field
     * @return $this
     */
    public function column($label, array|string|Closure $field = null): static
    {
        if ($field === null) {
            $field = $label;

            $label = null;
        }

        $this->last = uniqid('column');

        $key = Str::slug($this->model_name.(is_string($field) ? '_'.$field : ''), '_');

        $col = [
            'field' => $field,
            'label' => is_string($label) ? __($label) : $label,
            'sort' => false,
            'trash' => true,
            'info' => false,
            'key' => is_string($field) ? $key : null,
            'hide' => request()->has($key) && request($key) == 0,
            'macros' => [],
        ];

        if ($this->prepend) {
            $this->prepend = false;
            array_unshift($this->columns, $col);
        } else {
            $this->columns[$this->last] = $col;
        }

        return $this;
    }

    /**
     * Make the current table row only as export data.
     *
     * @return $this
     */
    public function only_export(callable $callback = null): static
    {
        if (isset($this->columns[$this->last])) {
            $this->to_export($callback);
            unset($this->columns[$this->last]);
        }

        return $this;
    }

    /**
     * Make the current table row for export.
     *
     * @return $this
     */
    public function to_export(callable $callback = null): static
    {
        if (isset($this->columns[$this->last])) {
            PrepareExport::$columns[$this->model_name][$this->last] = [
                'header' => $this->columns[$this->last]['label'],
                'field' => $callback ?: $this->columns[$this->last]['field'],
            ];
        }

        return $this;
    }

    /**
     * Make the current table row at the beginning of the list of rows.
     *
     * @return $this
     */
    public function to_prepend(): static
    {
        $this->prepend = true;

        return $this;
    }

    /**
     * Make the current table row not appear in the deleted records recycle bin table.
     *
     * @return $this
     */
    public function not_trash(): static
    {
        if (isset($this->columns[$this->last])) {
            $this->columns[$this->last]['trash'] = false;
        }

        return $this;
    }

    /**
     * Add information to the current column of the model table.
     *
     * @param  string  $info
     * @return $this
     */
    public function info(string $info): static
    {
        if (isset($this->columns[$this->last])) {
            $this->columns[$this->last]['info'] = $info;
        }

        return $this;
    }

    /**
     * Indicate that the current table row has a field with language variables.
     *
     * @param  string|null  $showLanguageCode
     * @return $this
     */
    public function lang(string $showLanguageCode = null): static
    {
        $showLanguageCode = $showLanguageCode ?: App::getLocale();

        if (
            isset($this->columns[$this->last])
            && is_string($this->columns[$this->last]['field'])
        ) {
            $this->columns[$this->last]['field'] .= ".{$showLanguageCode}";
        }

        return $this;
    }

    /**
     * Make the current table row hidden so it can be opened in actions.
     *
     * @return $this
     */
    public function to_hide(string $key = null): static
    {
        if ($key) {
            $this->columns[$this->last]['key']
                = $this->model_name.'_'.Str::slug($key, '_');
        }
        if (
            !$this->columns[$this->last]['key']
            && $this->columns[$this->last]['sort']
        ) {
            $this->columns[$this->last]['key']
                = $this->columns[$this->last]['sort'];
        }
        if (
            isset($this->columns[$this->last])
            && $this->columns[$this->last]['key']
        ) {
            $this->hasHidden = true;
            $this->columns[$this->last]['hide']
                = !(request($this->columns[$this->last]['key']) == 1);
        }

        if (isset($this->columns[$this->last]['key'])) {

            Admin::expectedQuery($this->columns[$this->last]['key']);
        }

        return $this;
    }

    /**
     * Helper for adding a table column with an identifier.
     *
     * @return $this
     */
    public function id(): static
    {
        if (collect($this->columns)->where('field', 'id')->isEmpty()) {

            $this->column('admin.id', 'id')->true_data()->hide_on_mobile()->sort();
        }

        return $this;
    }

    /**
     * Add sorting by the current table column.
     *
     * @param  string|null  $field
     * @return static
     */
    public function sort(string $field = null): static
    {
        if (isset($this->columns[$this->last])) {
            $this->columns[$this->last]['sort'] =
                $field ?
                    $field :
                    (
                    is_string($this->columns[$this->last]['field']) ?
                        $this->columns[$this->last]['field'] :
                        false
                    );
        }

        return $this;
    }

    /**
     * Helper for adding table columns "created at" and "updated at".
     *
     * @return $this
     */
    public function at(): static
    {
        $this->updated_at()->created_at();

        return $this;
    }

    /**
     * Helper for adding table column "created at".
     *
     * @return $this
     */
    public function created_at(): static
    {
        if (collect($this->columns)->where('field', 'created_at')->isEmpty()) {

            $this->column('admin.created_at', 'created_at')->beautiful_date_time()->true_data()->hide_on_mobile()->sort();
        }

        return $this;
    }

    /**
     * Helper for adding table column "updated at".
     *
     * @return $this
     */
    public function updated_at(): static
    {
        if (collect($this->columns)->where('field', 'updated_at')->isEmpty()) {

            $this->column('admin.updated_at', 'updated_at')->beautiful_date_time()->true_data()->hide_on_mobile()->sort();
        }

        return $this;
    }

    /**
     * Helper for adding table column "deleted at".
     *
     * @return $this
     */
    public function deleted_at(): static
    {
        if (collect($this->columns)->where('field', 'deleted_at')->isEmpty()) {

            $this->column('admin.deleted_at', 'deleted_at')->beautiful_date_time()->true_data()->hide_on_mobile()->sort();
        }

        return $this;
    }

    /**
     * Helper for adding a table column "active" with a radio button.
     *
     * @return $this
     */
    public function active_switcher(): static
    {
        $this->column('admin.active', 'active')->input_switcher()->hide_on_mobile()->sort();

        return $this;
    }

    /**
     * Get the unique current model name, if it does not exist, generate it.
     *
     * @return string
     */
    public function getModelName(): string
    {
        if ($this->model_name) {
            return $this->model_name;
        }

        $class = null;
        if ($this->model instanceof Model) {
            $class = get_class($this->model);
        } elseif ($this->model instanceof Builder) {
            $class = get_class($this->model->getModel());
        } elseif ($this->model instanceof Relation) {
            $class = get_class($this->model->getModel());
        } elseif (is_object($this->model)) {
            $class = get_class($this->model);
        } elseif (is_string($this->model)) {
            $class = $this->model;
        } elseif (is_array($this->model)) {
            $class = substr(md5(json_encode($this->model)), 0, 10);
        }
        $this->model_class = $class;
        $return = $class ? strtolower(class_basename($class)) : 'object_'.spl_object_id($this);
        $prep = '';
        if (isset(static::$models[$return])) {
            $prep .= static::$models[$return];
            static::$models[$return]++;
        } else {
            static::$models[$return] = 1;
        }

        return $this->model_name = $return.$prep;
    }

    /**
     * A method that generates a footer for a table.
     *
     * @return View|string
     */
    public function footer(): string|View
    {
        return $this->paginate ? admin_view('components.model-table.footer', $this->footerData()) : '';
    }

    /**
     * Get footer data for the table.
     *
     * @return array
     */
    public function footerData(): array
    {
        $paginator = $this->paginate;

        return [
            'total' => $paginator->total(),
            'hasPages' => $paginator->hasPages(),
            'onFirstPage' => $paginator->onFirstPage(),
            'currentPage' => $paginator->currentPage(),
            'hasMorePages' => $paginator->hasMorePages(),

            'from' => (($paginator->currentPage() * $paginator->perPage()) - $paginator->perPage()) + 1,
            'to' => min(($paginator->currentPage() * $paginator->perPage()), $paginator->total()),
            'per_page' => $this->per_page,
            'per_pages' => $this->per_pages,
            'elements' => $this->paginationElements($paginator),
            'page_name' => $this->model_name.'_page',
            'per_name' => $this->model_name.'_per_page',
        ];
    }

    /**
     * Set the number of entries per page.
     *
     * @param  int  $per_page
     * @return $this
     */
    public function perPage(int $per_page): static
    {
        if (is_int($this->per_page)) {
            $this->per_page = $per_page;
        }

        return $this;
    }

    /**
     * Magic method for adding line-by-line methods to tables or macros.
     *
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
     * Magic method for adding linked rows of table properties or macros.
     *
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
     * Checking the entire group of control buttons.
     *
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlGroup($test = null): static
    {
        $this->set_test_var('controls', $test);

        return $this;
    }

    /**
     * Checking the entire group of control buttons.
     *
     * @param  string  $var_name
     * @param $test
     */
    protected function set_test_var(string $var_name, $test): void
    {
        if (is_embedded_call($test)) {
            $this->{$var_name} = $test;
        } else {
            $this->{$var_name} = static function () use ($test) {
                return (bool) $test;
            };
        }
    }

    /**
     * Checking the entire group of information buttons.
     *
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlInfo($test = null): static
    {
        $this->set_test_var('control_info', $test);

        return $this;
    }

    /**
     * Checking the entire group of edit buttons.
     *
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlEdit($test = null): static
    {
        $this->set_test_var('control_edit', $test);

        return $this;
    }

    /**
     * Checking the entire group of delete buttons.
     *
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlDelete($test = null): static
    {
        $this->set_test_var('control_delete', $test);

        return $this;
    }

    /**
     * Checking the entire group of force delete buttons.
     *
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlForceDelete($test = null): static
    {
        $this->set_test_var('control_force_delete', $test);

        return $this;
    }

    /**
     * Checking the entire group of recovery buttons.
     *
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlRestore($test = null): static
    {
        $this->set_test_var('control_restore', $test);

        return $this;
    }

    /**
     * Checking whether it is possible to select a card in the table.
     *
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlSelect($test = null): static
    {
        $this->set_test_var('control_selectable', $test);

        return $this;
    }

    /**
     * Check mass deletion is possible.
     *
     * @param  null  $test
     * @return $this
     */
    public function checkDelete($test = null): static
    {
        $this->set_test_var('check_delete', $test);

        return $this;
    }

    /**
     * Disable highlighting of cards on the table.
     *
     * @return $this
     */
    public function disableChecks(): static
    {
        $this->checks = false;

        return $this;
    }

    /**
     * Add a new card action to the table.
     *
     * @param  callable  $callback
     * @param  array  $parameter
     * @return \Admin\Core\ModelTableAction
     */
    public function action(callable $callback, array $parameter = []): ModelTableAction
    {
        return $this->action[] = new ModelTableAction(
            $this->model,
            $callback,
            $parameter
        );
    }

    /**
     * Get data on actions.
     *
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getActionData(): array
    {
        $m = $this->realModel();
        $this->model_class = $this->realModel() && is_object($m) ? get_class($m) : null;
        $hasDelete = $this->menu
            && $this->get_test_var('check_delete')
            && $this->menu->isResource()
            && $this->menu->getLinkDestroy(0);
        $select_type = request()->get($this->model_name.'_type', $this->order_type);
        $this->order_field = request()->get($this->model_name, $this->order_field);

        return [
            'table_id' => $this->model_name,
            'object' => $this->model_class,
            'hasHidden' => $this->hasHidden,
            'hasDelete' => $hasDelete,
            'show' => (count($this->action) || $hasDelete || count(PrepareExport::$columns) || $this->hasHidden) && $this->checks,
            'actions' => array_map(fn(ModelTableAction $action) => $action->toArray(), $this->action),
            'order_field' => $this->order_field,
            'select_type' => $select_type,
            'columns' => collect($this->columns)
                ->filter(static function ($i) {
                    return isset($i['field']) && is_string($i['field']) && !$i['hide'];
                })
                ->pluck('field')
                ->toArray(),
            'all_columns' => collect($this->columns)
                ->filter(static function ($i) {
                    return isset($i['label']) && $i['label'];
                })
                ->map(static function ($i) {
                    unset($i['macros']);

                    return $i;
                })
                ->toArray(),
        ];
    }

    /**
     * Get the value of the variable to check.
     *
     * @param  string  $var_name
     * @param  array  $args
     * @return bool
     */
    protected function get_test_var(string $var_name, array $args = []): bool
    {
        if ($this->{$var_name} !== null) {
            return call_user_func_array($this->{$var_name}, $args);
        }

        return true;
    }

    /**
     * Create default controls.
     *
     * @return void
     */
    protected function createControls(): void
    {
        if ($this->get_test_var('controls')) {
            $hasDelete = $this->menu
                && $this->menu->isResource()
                && $this->menu->getLinkDestroy(0);
            $show = count($this->action) || $hasDelete || count(PrepareExport::$columns) || $this->hasHidden;
            $modelName = $this->model_name;

            if ($this->checks && !request()->has('show_deleted') && $show) {
                $this->to_prepend()->column(function (HeaderComponent $headerComponent) use ($hasDelete) {
                    $headerComponent->fit();

                    $headerComponent->view('components.model-table.checkbox', [
                        'id' => false,
                        'table_id' => $this->model_name,
                        'object' => $this->model_class,
                        'actions' => $this->action,
                        'delete' => $this->get_test_var('check_delete') && $hasDelete,
                        'columns' => collect($this->columns)->filter(static function ($i) {
                            return isset($i['field']) && is_string($i['field']);
                        })->pluck('field')->toArray(),
                    ]);
                }, function (Model|array $model) use ($modelName) {
                    return admin_view('components.model-table.checkbox', [
                        'id' => is_array($model) ? ($model['id'] ?? null) : $model->id,
                        'table_id' => $modelName,
                        'disabled' => !$this->get_test_var('control_selectable', [$model]),
                    ]);
                });
            }

            if (request()->has('show_deleted')) {
                $this->deleted_at();
            }

            $this->column(function (HeaderComponent $headerComponent) {
                $headerComponent->fit();
            }, function (Model|array $model) {
                $menu = $this->menu;

                return $this->createComponent(ButtonsComponent::class)->use(function (ButtonsComponent $group) use (
                    $model,
                    $menu
                ) {
                    if ($menu && $menu->isResource()) {
                        $key = $model->getRouteKey();

                        if (!request()->has('show_deleted')) {
                            if ($this->get_test_var('control_edit', [$model])) {
                                if (AdminPermission::checkUrl($menu->getLinkEdit($key), 'PUT')) {
                                    $group->resourceEdit($menu->getLinkEdit($key), '');
                                }
                            }

                            if ($this->get_test_var('control_delete', [$model])) {
                                if (AdminPermission::checkUrl($menu->getLinkDestroy($key), 'DELETE')) {
                                    $group->resourceDestroy(
                                        $menu->getLinkDestroy($key),
                                        '',
                                        $model->getRouteKeyName(),
                                        $key,
                                        ['_after' => 'stay']
                                    );
                                }
                            }

                            if ($this->get_test_var('control_info', [$model])) {
                                if (AdminPermission::checkUrl($menu->getLinkShow($key), 'GET')) {
                                    $group->resourceInfo($menu->getLinkShow($key), '');
                                }
                            }
                        } else {
                            if ($this->get_test_var('control_restore', [$model])) {
                                $group->resourceRestore(
                                    $menu->getLinkDestroy($key),
                                    '',
                                    $model->getRouteKeyName(),
                                    $key
                                );
                            }

                            if ($this->get_test_var('control_force_delete', [$model])) {
                                $group->resourceForceDestroy(
                                    $menu->getLinkDestroy($key),
                                    '',
                                    $model->getRouteKeyName(),
                                    $key
                                );
                            }
                        }
                    }
                });
            });
        }
    }

    /**
     * A function that generates card model data and paginates it.
     *
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createModel(): mixed
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

            return $this->paginate = $this->model
                ->when($this->relations, fn ($q) => $q->with(...$this->relations))
                ->orderBy($this->order_field, $select_type)
                ->paginate($this->per_page, ['*'], $this->model_name.'_page');

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
     * Build the component and its internal parts.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function build(): void
    {
        $header = $this->createComponent(HeadComponent::class);

        $this->appEnd($header);

        $header_count = 0;

        foreach ($this->columns as $key => $column) {
            if ((request()->has('show_deleted') && !$column['trash']) || $column['hide']) {
                continue;
            }

            $this->makeHeadTH($header, $column, $key);

            $header_count++;
        }

        $body = $this->createComponent(BodyComponent::class);

        $this->appEnd($body);

        foreach ($this->paginate ?? $this->model as $item) {
            $row = RowComponent::create();
            $this->makeBodyTR($row, $item);
            $body->appEnd($row);
        }

        $count = 0;

        if (is_array($this->model)) {
            $count = count($this->model);
        } elseif ($this->paginate) {
            $count = $this->paginate->count();
        }

        if (!$count) {
            $body->view('components.model-table.empty', [
                'header_count' => $header_count
            ]);
        }
    }

    /**
     * Create model table headers.
     *
     * @param  HeadComponent  $head
     * @param  array  $column
     * @param  string|int  $key
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function makeHeadTH(HeadComponent $head, array $column, string|int $key): void
    {
        $select = request()->get($this->model_name.'_type', $this->order_type);
        $now = request()->get($this->model_name, $this->order_field) == $column['sort'];
        $type = $now ? ($select === 'desc' ? 'down' : 'up-alt') : 'down';

        $header = $head->createComponent(HeaderComponent::class);
        $header->setViewData([
            'column' => $column,
            'model_name' => $this->model_name,
            'now' => $now,
            'select' => $select,
            'type' => $type,
        ]);
        $this->columns[$key]['header'] = $header;
        $head->appEnd($header);
    }

    /**
     * Create model table rows.
     *
     * @param  RowComponent  $row
     * @param $item
     */
    protected function makeBodyTR(RowComponent $row, $item): void
    {
        foreach ($this->columns as $column) {
            $value = $column['field'];

            if ((request()->has('show_deleted') && !$column['trash']) || $column['hide']) {
                continue;
            }

            $columnComponent = $row->createComponent(ColumnComponent::class);

            if (is_string($value)) {
                $ddd = multi_dot_call($item, $value);
                $value = is_array($ddd) || is_object($ddd) ? $ddd : e($ddd);
            } elseif (is_embedded_call($value)) {
                $value = call_user_func_array($value, [
                    $item, $column['label'], $columnComponent, $column['header'], $row,
                ]);
            }
            foreach ($column['macros'] as $macro) {
                $value = static::callE($macro[0], [
                    $value, $macro[1], $item, $column['field'], $column['label'], $columnComponent, $column['header'],
                    $row,
                ]);
            }

            $columnComponent->setViewData([
                'value' => $value
            ]);

            $row->appEnd($columnComponent);
        }
    }

    /**
     * Get the array of elements to pass to the view.
     *
     * @param  LengthAwarePaginator  $page
     * @return array
     */
    protected function paginationElements(LengthAwarePaginator $page): array
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
     * Additional data to be sent to the template.
     *
     * @return string[]
     */
    protected function viewData(): array
    {
        return [
            'id' => $this->model_name,
        ];
    }

    /**
     * Api data.
     *
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function apiData(): array
    {
        Admin::important($this->model_name, $this->paginate, $this->getResource());
        Admin::expectedQuery($this->model_name);
        Admin::expectedQuery($this->model_name.'_type');
        Admin::expectedQuery($this->model_name.'_per_page');
        Admin::expectedQuery($this->model_name.'_page');
        Admin::expectedQuery('show_deleted');
        Admin::expectedQuery('q');

        $header = $this->getActionData();

        return [
            'id' => $this->model_name,
            'header' => $header ? [
                'show' => $header['show'],
                'columns' => $header['columns'],
                'selectType' => $header['select_type'],
                'orderField' => $header['order_field'],
                'actions' => $header['actions'],
                'hasHidden' => $header['hasHidden'],
                'hasDelete' => $header['hasDelete'],
                'allColumns' => collect($header['all_columns'])->map(function ($obj) {
                    if ($obj['header'] ?? null) {
                        $obj['header'] = $obj['header']->exportToApi();
                    }
                    return $obj;
                })->values(),
            ] : [],
            'columns' => collect($this->columns)->map(function (array $col) {
                if (isset($col['header'])) {
                    $col['header'] = collect($col['header']->exportToApi())->collapse();
                }
                return $col;
            }),
            'order_type' => request()->get($this->model_name.'_type', $this->order_type),
            'model_name' => $this->model_name,
            'order_field' => $this->order_field,
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function mount(): void
    {
        $this->build();
    }
}
