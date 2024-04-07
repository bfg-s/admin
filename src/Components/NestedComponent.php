<?php

declare(strict_types=1);

namespace Admin\Components;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class NestedComponent extends Component
{
    /**
     * @var string
     */
    protected string $view = 'nested';

    /**
     * @var Builder|Model|Relation|string|null
     */
    protected $model;

    /**
     * @var string|callable
     */
    protected $title_field = 'name';

    /**
     * @var string
     */
    protected string $parent_field = 'parent_id';

    /**
     * Shoe default controls.
     *
     * @var mixed
     */
    protected mixed $controls = null;

    /**
     * Custom controls.
     *
     * @var callable
     */
    protected $custom_controls;

    /**
     * @var mixed
     */
    protected mixed $info_control = null;

    /**
     * @var mixed
     */
    protected mixed $delete_control = null;

    /**
     * @var mixed
     */
    protected mixed $edit_control = null;

    /**
     * @var string
     */
    private string $order_by_field = 'order';

    /**
     * @var string
     */
    private string $order_by_type = 'asc';

    /**
     * @var int
     */
    private int $max_depth = 5;

    /**
     * Col constructor.
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

        $this->setDatas(['load' => 'nestable']);
    }

    /**
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
     * @param  string|callable  $field
     * @return $this
     */
    public function titleField(string|callable $field): static
    {
        $this->title_field = $field;

        return $this;
    }

    /**
     * @param ...$delegates
     * @return $this
     */
    public function template(...$delegates): static
    {
        $this->title_field = $delegates;

        return $this;
    }

    /**
     * @param  string|callable  $field
     * @return $this
     */
    public function parentField(string|callable $field): static
    {
        $this->parent_field = $field;

        return $this;
    }

    /**
     * @param  int  $depth
     * @return $this
     */
    public function maxDepth(int $depth): static
    {
        $this->max_depth = $depth;

        return $this;
    }

    /**
     * @param  callable  $call
     * @return $this
     */
    public function controls(callable $call): static
    {
        $this->custom_controls = $call;

        return $this;
    }

    /**
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

    /**
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
}
