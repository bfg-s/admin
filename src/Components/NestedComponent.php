<?php

declare(strict_types=1);

namespace Admin\Components;

/**
 * Component of nested control and order of the admin panel.
 */
class NestedComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'nested';

    /**
     * Model field that should be used as the item name.
     *
     * @var string|callable
     */
    protected $title_field = 'name';

    /**
     * The parent field of the model.
     *
     * @var string
     */
    protected string $parent_field = 'parent_id';

    /**
     * Show default controls.
     *
     * @var mixed
     */
    protected mixed $controls = null;

    /**
     * Callback for adding custom buttons.
     *
     * @var mixed
     */
    protected mixed $custom_controls = null;

    /**
     * Property for checking whether an item's information button is displayed.
     *
     * @var mixed
     */
    protected mixed $info_control = null;

    /**
     * Property for checking whether the item delete button is displayed.
     *
     * @var mixed
     */
    protected mixed $delete_control = null;

    /**
     * Property for checking whether the item edit button is displayed.
     *
     * @var mixed
     */
    protected mixed $edit_control = null;

    /**
     * The model field by which data is sorted.
     *
     * @var string
     */
    private string $order_by_field = 'order';

    /**
     * The current data sort type.
     *
     * @var string
     */
    private string $order_by_type = 'asc';

    /**
     * Maximum nesting depth (if nesting is present).
     *
     * @var int
     */
    private int $max_depth = 5;

    /**
     * Realtime marker, if enabled, the component will be updated at the specified frequency.
     *
     * @var bool
     */
    protected bool $realTime = true;

    /**
     * NestedComponent constructor.
     *
     * @param  mixed  ...$delegates
     */
    public function __construct(...$delegates)
    {
        $this->title_field = config('admin.nested-component.title_field', $this->title_field);
        $this->parent_field = config('admin.nested-component.parent_field', $this->parent_field);
        $this->order_by_field = config('admin.nested-component.order_by_field', $this->order_by_field);
        $this->order_by_type = config('admin.nested-component.order_by_type', $this->order_by_type);
        $this->max_depth = config('admin.nested-component.max_depth', $this->max_depth);

        $this->controls =
        $this->info_control =
        $this->delete_control =
        $this->edit_control = static function () {
            return true;
        };

        parent::__construct(...$delegates);

        $this->dataLoad('nestable');
    }

    /**
     * Set the sorting type to "desc" and, if necessary, the field by which sorting is performed.
     *
     * @param  string|null  $field
     * @return $this
     */
    public function orderDesc(string $field = null): static
    {
        $this->order_by_type = 'desc';

        if ($field) {
            $this->order_by_field = $field;
        }

        return $this;
    }

    /**
     * Set the model field that is responsible for the item title.
     *
     * @param  string|callable  $field
     * @return $this
     */
    public function titleField(string|callable $field): static
    {
        $this->title_field = $field;

        return $this;
    }

    /**
     * Set the model's parent field.
     *
     * @param  string|callable  $field
     * @return $this
     */
    public function parentField(string|callable $field): static
    {
        $this->parent_field = $field;

        return $this;
    }

    /**
     * Set the maximum nesting depth of models.
     *
     * @param  int  $depth
     * @return $this
     */
    public function maxDepth(int $depth): static
    {
        $this->max_depth = $depth;

        return $this;
    }

    /**
     * Add a callback to add custom buttons.
     *
     * @param  callable  $call
     * @return $this
     */
    public function controls(callable $call): static
    {
        $this->custom_controls = $call;

        return $this;
    }

    /**
     * Checking all control buttons.
     *
     * @param  mixed  $test
     * @return $this
     */
    public function disableControls(mixed $test = null): static
    {
        $this->controls = is_embedded_call($test) ? $test : static function () {
            return false;
        };

        return $this;
    }

    /**
     * Check all info buttons.
     *
     * @param  mixed  $test
     * @return $this
     */
    public function disableInfo(mixed $test = null): static
    {
        $this->info_control = is_embedded_call($test) ? $test : static function () {
            return false;
        };

        return $this;
    }

    /**
     * Checking all edit buttons.
     *
     * @param  mixed  $test
     * @return $this
     */
    public function disableEdit(mixed $test = null): static
    {
        $this->edit_control = is_embedded_call($test) ? $test : static function () {
            return false;
        };

        return $this;
    }

    /**
     * Checking all delete buttons.
     *
     * @param  mixed  $test
     * @return $this
     */
    public function disableDelete(mixed $test = null): static
    {
        $this->delete_control = is_embedded_call($test) ? $test : static function () {
            return false;
        };

        return $this;
    }

    /**
     * Set the field to be sorted and the sort type.
     *
     * @param  string|null  $field
     * @param  string|null  $order
     * @return $this
     */
    public function orderBy(string $field = null, string $order = null): static
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
     * Additional data to be sent to the template.
     *
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'models' => $this->model,
            'controls' => $this->controls,
            'cc' => $this->custom_controls,
            'menu' => $this->menu,
            'title_field' => $this->title_field,
            'parent_field' => $this->parent_field,
            'maxDepth' => $this->max_depth,
            'buttons' => function ($item, $cc_access, $cc) {
                $group = ButtonsComponent::create();
                $model = $item;
                $key = $model->getRouteKey();

                if ($cc) {
                    call_user_func($cc, $group, $model);
                }

                if ($cc_access) {
                    if (($this->edit_control)($item)) {
                        $group->resourceEdit($this->menu->getLinkEdit($key), '');
                    }

                    if (($this->delete_control)($item)) {
                        $group->resourceDestroy(
                            $this->menu->getLinkDestroy($key),
                            '',
                            $model->getRouteKeyName(),
                            $key
                        );
                    }

                    if (($this->info_control)($item)) {
                        $group->resourceInfo($this->menu->getLinkShow($key), '');
                    }
                }
                return $group;
            },
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        $model = $this->realModel();

        $hasOrder = false;

        $this->setDatas([
            'route' => route('admin.nestable_save')
        ]);

        if ($model) {
            $fillable = $model->getFillable();
            if (!in_array($this->parent_field, $fillable)) {
                $this->max_depth = 1;
            }
            if (in_array($this->order_by_field, $fillable)) {
                $hasOrder = true;
            }
            $this->setDatas(['model' => get_class($model)]);
        } else {
            $this->max_depth = 1;
        }

        $this->setDatas(['max-depth' => $this->max_depth, 'parent' => $this->parent_field]);

        if ($hasOrder) {
            $this->model = $this->model->orderBy($this->order_by_field, $this->order_by_type);
        }

        $this->model = $this->model->get();

        $this->attr('data-order-field', $this->order_by_field);
    }
}
