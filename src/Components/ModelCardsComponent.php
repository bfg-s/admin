<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Components\ModelCards\CardComponent;
use Admin\Components\ModelTable\HeadComponent;
use Admin\Core\ModelTableAction;
use Admin\Core\PrepareExport;
use Admin\Facades\Admin;
use Admin\Models\AdminPermission;
use Admin\Traits\FontAwesomeTrait;
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
 * Component of model cards of the admin panel.
 *
 * @methods Admin\Components\ModelTableComponent::$extensions (...$params) static
 * @mixin ModelCardsComponentFields
 * @mixin ModelCardsComponentMethods
 * @property-read ModelCardsComponent $sort
 */
class ModelCardsComponent extends Component
{
    use FontAwesomeTrait;
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
    protected string $view = 'model-cards';

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
     * Number of cards per page.
     *
     * @var int
     */
    protected int $per_page = 9;

    /**
     * Possible number of cards that can be displayed on one page.
     *
     * @var int[]
     */
    protected array $per_pages = [9, 15, 21, 51, 102, 501, 1002];

    /**
     * The field by which cards are sorted.
     *
     * @var string
     */
    protected string $order_field = 'id';

    /**
     * The type by which cards are sorted.
     *
     * @var string
     */
    protected string $order_type = 'desc';

    /**
     * All rows of the card.
     *
     * @var array
     */
    protected array $rows = [];

    /**
     * Last row index.
     *
     * @var string|null
     */
    protected string|null $last;

    /**
     * Add a row to the beginning of a list of rows.
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
     * Groups of custom model card buttons.
     *
     * @var array
     */
    protected array $buttons = [];

    /**
     * Avatar field name.
     *
     * @var string|null
     */
    protected string|null $avatarField = null;

    /**
     * The name of the card title field.
     *
     * @var string|null
     */
    protected string|null $titleField = null;

    /**
     * The name of the card subtitle field.
     *
     * @var string|null
     */
    protected string|null $subtitleField = null;

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
     * Property for checking whether the table has a ribbon.
     *
     * @var \Closure|array|null
     */
    protected Closure|array|null $ribbon = null;

    /**
     * Property for enabling or disabling table row selection checkboxes.
     *
     * @var bool
     */
    protected bool $checks = true;

    /**
     * Property for enabling or disabling the deletion of table rows selected by checkboxes.
     *
     * @var Closure|array|null
     */
    protected Closure|array|null $check_delete = null;

    /**
     * Actions with table rows highlighted by checkboxes.
     *
     * @var array
     */
    protected array $action = [];

    /**
     * This property contains a closure for creating a checkbox.
     *
     * @var Closure|null
     */
    protected Closure|null $checkBox = null;

    /**
     * Realtime marker, if enabled, the component will be updated at the specified frequency.
     *
     * @var bool
     */
    protected bool $realTime = true;

    /**
     * The origin of the rows.
     *
     * @var array
     */
    protected array $originRows = [];

    /**
     * ModelCardsComponent constructor.
     *
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        $this->per_page = config('admin.model-cards-component.per_page', $this->per_page);
        $this->per_pages = config('admin.model-cards-component.per_pages', $this->per_pages);
        $this->order_field = config('admin.model-cards-component.order_field', $this->order_field);
        $this->order_type = config('admin.model-cards-component.order_type', $this->order_type);

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
            preg_match("/^row_(.+)$/", $name, $matches)
            && !isset(Component::$inputs[$name])
            && !Component::hasComponentStatic($name)
        ) {
            $name = str_replace(['_dot_', '__'], '.', Str::snake($matches[1], '_'));
            $label = $arguments[0] ?? ucfirst(str_replace(['.', '_'], ' ', $name));

            return $this->row(Lang::has("admin.$label") ? __("admin.$label") : $label, $name);
        } else {
            if (ModelTableComponent::hasExtension($name) && isset($this->rows[$this->last])) {
                $this->rows[$this->last]['macros'][] = [$name, $arguments];

                return $this;
            }
        }

        return parent::__call($name, $arguments);
    }

    /**
     * Magic method for adding linked rows of table properties or macros.
     *
     * @param  string  $name
     * @return ModelCardsComponent
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

            return $this->row(Lang::has("admin.$name") ? __("admin.$name") : $label, $name);
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
        $this->order_field = $this->model_name
            ? request()->get($this->model_name, $this->order_field)
            : $this->order_field;

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
     * Set the name of the model field in which the avatar or any other image is located; if the field is not specified, the image will not be displayed.
     *
     * @param  string  $field
     * @return $this
     */
    public function avatarField(string $field): static
    {
        $this->avatarField = $field;

        return $this;
    }

    /**
     * Set the name of the model field in which the title for the card is located; if the field is not specified, the title will not be displayed.
     *
     * @param  string  $field
     * @return $this
     */
    public function titleField(string $field): static
    {
        $this->titleField = $field;

        return $this;
    }

    /**
     * Set the name of the model field in which the subtitle for the card is located; if the field is not specified, the subtitle will not be displayed.
     *
     * @param  string  $field
     * @return $this
     */
    public function subtitleField(string $field): static
    {
        $this->subtitleField = $field;

        return $this;
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
        $this->buttons[] = $this->createComponent(ButtonsComponent::class)
            ->delegatesNow($delegates);

        return $this;
    }

    /**
     * Make the current table row only as export data.
     *
     * @return $this
     */
    public function only_export(callable $callback = null): static
    {
        if (isset($this->rows[$this->last])) {
            $this->to_export($callback);
            unset($this->rows[$this->last]);
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
        if (isset($this->rows[$this->last])) {
            PrepareExport::$columns[$this->model_name][$this->last] = [
                'header' => $this->rows[$this->last]['label'],
                'field' => $callback ?: $this->rows[$this->last]['field'],
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
        if (isset($this->rows[$this->last])) {
            $this->rows[$this->last]['trash'] = false;
        }

        return $this;
    }

    /**
     * Set the current table row icon.
     *
     * @return $this
     */
    public function icon(string $icon): static
    {
        if (isset($this->rows[$this->last])) {
            $this->rows[$this->last]['icon'] = $icon;
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
            isset($this->rows[$this->last])
            && is_string($this->rows[$this->last]['field'])
        ) {
            $this->rows[$this->last]['field'] .= ".{$showLanguageCode}";
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
            $this->rows[$this->last]['key']
                = $this->model_name.'_'.Str::slug($key, '_');
        }
        if (
            !$this->rows[$this->last]['key']
            && $this->rows[$this->last]['sort']
        ) {
            $this->rows[$this->last]['key']
                = $this->rows[$this->last]['sort'];
        }
        if (
            isset($this->rows[$this->last])
            && $this->rows[$this->last]['key']
        ) {
            $this->hasHidden = true;
            $this->rows[$this->last]['hide']
                = !(request($this->rows[$this->last]['key']) == 1);
        }

        return $this;
    }

    /**
     * Helper for adding a table row with an identifier.
     *
     * @return $this
     */
    public function id(): static
    {
        $this->row('admin.id', 'id')->true_data()->sort()->icon_hashtag();

        return $this;
    }

    /**
     * Add sorting by the current table row.
     *
     * @param  string|null  $field
     * @return static
     */
    public function sort(string $field = null): static
    {
        if (isset($this->rows[$this->last])) {
            $this->rows[$this->last]['sort'] =
                $field ?: (
                is_string($this->rows[$this->last]['field']) ?
                    $this->rows[$this->last]['field'] :
                    false
                );
        }

        return $this;
    }

    /**
     * Add a table row to the card.
     *
     * @param  string|Closure|array|null  $label
     * @param  array|string|Closure|null  $field
     * @return $this
     */
    public function row($label, array|string|Closure $field = null): static
    {
        if ($field === null) {
            $field = $label;

            $label = null;
        }

        $this->last = uniqid('row');

        $key = Str::slug($this->model_name.(is_string($field) ? '_'.$field : ''), '_');

        $row = [
            'field' => $field,
            'label' => is_string($label) ? __($label) : $label,
            'sort' => false,
            'trash' => true,
            'info' => false,
            'icon' => false,
            'key' => is_string($field) ? $key : null,
            'hide' => request()->has($key) && request($key) == 0,
            'macros' => [],
        ];

        if ($this->prepend) {
            $this->prepend = false;
            array_unshift($this->rows, $row);
        } else {
            $this->rows[$this->last] = $row;
        }

        return $this;
    }

    /**
     * Helper for adding table rows "created at" and "updated at".
     *
     * @return $this
     */
    public function at(): static
    {
        $this->updated_at()->created_at();

        return $this;
    }

    /**
     * Helper for adding table row "created at".
     *
     * @return $this
     */
    public function created_at(): static
    {
        $this->row('admin.created_at', 'created_at')->beautiful_date_time()->true_data()->sort()->icon_clock();

        return $this;
    }

    /**
     * Helper for adding table row "updated at".
     *
     * @return $this
     */
    public function updated_at(): static
    {
        $this->row('admin.updated_at', 'updated_at')->beautiful_date_time()->true_data()->sort()->icon_clock();

        return $this;
    }

    /**
     * Helper for adding table row "deleted at".
     *
     * @return $this
     */
    public function deleted_at(): static
    {
        $this->row('admin.deleted_at', 'deleted_at')->beautiful_date_time()->true_data()->sort()->icon_clock();

        return $this;
    }

    /**
     * Helper for adding a table row "active" with a radio button.
     *
     * @return $this
     */
    public function active_switcher(): static
    {
        $this->row('admin.active', 'active')->input_switcher()->sort();

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
        return $this->paginate ? admin_view('components.model-cards.footer', $this->footerData()) : '';
    }

    /**
     * Get and generate the footer data for the table.
     *
     * @return array
     */
    public function footerData(): array
    {
        $paginator = $this->paginate;

        return $paginator ? [
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
        ] : [];
    }

    /**
     * Set the number of entries per page.
     *
     * @param  int  $per_page
     * @return $this
     */
    public function perPage(int $per_page): static
    {
        $this->per_page = $per_page;

        return $this;
    }

    /**
     * Build the component and its internal parts.
     *
     * @return void
     */
    protected function build(): void
    {
        $header = $this->createComponent(HeadComponent::class);

        $this->appEnd($header);

        $header_count = 0;

        foreach ($this->rows as $key => $row) {
            if ((request()->has('show_deleted') && !$row['trash']) || $row['hide']) {
                continue;
            }

            $header_count++;
        }

        if ($this->paginate || $this->model) {

            foreach ($this->paginate ?: $this->model as $item) {
                $this->makeBodyCard($item);
            }
        }

        $count = 0;

        if (is_array($this->model)) {
            $count = count($this->model);
        } elseif ($this->paginate) {
            $count = $this->paginate->count();
        }

        if (!$count) {
            $this->view('components.model-cards.empty', [
                'header_count' => $header_count
            ]);
        }
    }

    /**
     * Checking the entire group of control buttons.
     *
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlGroup(mixed $test = null): static
    {
        $this->set_test_var('controls', $test);

        return $this;
    }

    /**
     * Checking the entire group of information buttons.
     *
     * @param  Closure|array|mixed  $test
     * @return $this
     */
    public function controlInfo(mixed $test = null): static
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
    public function controlEdit(mixed $test = null): static
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
    public function controlDelete(mixed $test = null): static
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
    public function controlForceDelete(mixed $test = null): static
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
    public function controlRestore(mixed $test = null): static
    {
        $this->set_test_var('control_restore', $test);

        return $this;
    }

    /**
     * Checking whether it is possible to select a card in the table.
     *
     * @param  mixed|null  $test
     * @return $this
     */
    public function controlSelect(mixed $test = null): static
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
    public function checkDelete(mixed $test = null): static
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
        //$this->getModelName();
        $m = $this->realModel();
        $this->model_class = $this->realModel() && is_object($m) ? get_class($m) : null;
        $hasDelete = $this->menu
            && $this->get_test_var('check_delete')
            && $this->menu->isResource()
            && $this->menu->getLinkDestroy(0);
        $select_type = request()->get($this->model_name.'_type', $this->order_type);
        $this->order_field = $this->model_name ? request()->get($this->model_name, $this->order_field) : $this->order_field;

        return [
            'table_id' => $this->model_name,
            'object' => $this->model_class,
            'hasHidden' => $this->hasHidden,
            'hasDelete' => $hasDelete,
            'show' => (count($this->action) || $hasDelete || count(PrepareExport::$columns) || $this->hasHidden) && $this->checks,
            'actions' => array_map(fn(ModelTableAction $action) => $action->toArray(), $this->action),
            'order_field' => $this->order_field,
            'select_type' => $select_type,
            'columns' => collect($this->rows)
                ->filter(static function ($i) {
                    return isset($i['field']) && is_string($i['field']) && !$i['hide'];
                })
                ->pluck('field')
                ->toArray(),
            'all_columns' => collect($this->rows)
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
     * Set the table row ribbon.
     *
     * @param  callable|null  $test
     * @return $this
     */
    public function ribbon(callable $test = null): static
    {
        $this->ribbon = $test;

        return $this;
    }

    /**
     * Set the value of the variable to check.
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
            $this->checkBox = function (Model|array $model) use ($modelName) {
                return admin_view('components.model-table.checkbox', [
                    'id' => is_array($model) ? ($model['id'] ?? null) : $model->id,
                    'table_id' => $modelName,
                    'disabled' => !$this->get_test_var('control_selectable', [$model]),
                ]);
            };

            if (request()->has('show_deleted')) {
                $this->deleted_at();
            }

            $this->buttons[] = function (Model|array $model) {
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
            };
        }
    }

    /**
     * Function for generating internal cards.
     *
     * @param $model Model|array
     */
    protected function makeBodyCard(mixed $model): void
    {
        $cardComponent = $this->createComponent(CardComponent::class);

        $this->originRows = $this->rows;

        foreach ($this->rows as $key => $row) {
            $value = $row['field'];

            if ((request()->has('show_deleted') && !$row['trash']) || $row['hide']) {
                continue;
            }

            if (is_string($value)) {
                $ddd = multi_dot_call($model, $value);
                $value = is_array($ddd) || is_object($ddd) ? $ddd : e($ddd);
            } elseif (is_embedded_call($value)) {
                $value = call_user_func_array($value, [
                    $model, $row['label'], $cardComponent, null, $row,
                ]);
            }

            $this->originRows[$key]['value'] = $value && is_string($value) ? strip_tags($value) : $value;

            foreach ($row['macros'] as $macro) {
                $value = ModelTableComponent::callE($macro[0], [
                    $value, $macro[1], $model, $row['field'], $row['label'], $cardComponent, null, $row,
                ]);
            }

            $this->rows[$key]['value'] = $value;
        }

        $cardComponent->setViewData([
            'rows' => $this->rows,
            'model' => $model,
            'avatarField' => $this->avatarField,
            'titleField' => $this->titleField,
            'subtitleField' => $this->subtitleField,
            'buttons' => $this->buttons,
            'checkBox' => $this->checkBox,
            'ribbon' => $this->ribbon ? call_user_func($this->ribbon, $model) : '',
        ]);

        $this->appEnd($cardComponent);
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function viewData(): array
    {
        $select = request()->get($this->model_name.'_type', $this->order_type);

        return [
            'id' => $this->model_name,
            'rows' => $this->rows,
            'select' => $select,
            'model_name' => $this->model_name,
            'order_field' => $this->order_field,
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
        if ($this->model_name) {
            Admin::important($this->model_name, $this->paginate, $this->getResource());
            Admin::expectedQuery($this->model_name);
            Admin::expectedQuery($this->model_name.'_type');
            Admin::expectedQuery($this->model_name.'_per_page');
            Admin::expectedQuery($this->model_name.'_page');
            Admin::expectedQuery('show_deleted');
            Admin::expectedQuery('q');
        }

        $header = $this->getActionData();

        return [
            'id' => $this->model_name,
            'header' => $header ? [
                'show' => $header['show'],
                'rows' => $header['columns'],
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
            'rows' => $this->originRows,
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
