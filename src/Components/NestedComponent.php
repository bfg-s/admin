<?php

namespace LteAdmin\Components;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\LI;
use Lar\Layout\Tags\OL;

class NestedComponent extends Component
{
    /**
     * @var string[]
     */
    protected $props = [
        'dd',
    ];

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
    protected $parent_field = 'parent_id';

    /**
     * Shoe default controls.
     *
     * @var Closure|array
     */
    protected $controls;

    /**
     * Custom controls.
     *
     * @var callable
     */
    protected $custom_controls;

    /**
     * @var Closure|array
     */
    protected $info_control;

    /**
     * @var Closure|array
     */
    protected $delete_control;

    /**
     * @var Closure|array
     */
    protected $edit_control;

    /**
     * @var string
     */
    private $order_by_field = 'order';

    /**
     * @var string
     */
    private $order_by_type = 'asc';

    /**
     * @var int
     */
    private $maxDepth = 5;

    /**
     * Col constructor.
     * @param  null  $model
     */
    public function __construct(...$delegates)
    {
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
     * @param  string|callable  $field
     * @return $this
     */
    public function titleField(string|callable $field)
    {
        $this->title_field = $field;

        return $this;
    }

    public function template(...$delegates)
    {
        $this->title_field = $delegates;

        return $this;
    }

    /**
     * @param  string|callable  $field
     * @return $this
     */
    public function parentField(string|callable $field)
    {
        $this->parent_field = $field;

        return $this;
    }

    /**
     * @param  int  $depth
     * @return $this
     */
    public function maxDepth(int $depth)
    {
        $this->maxDepth = $depth;

        return $this;
    }

    /**
     * @param  callable  $call
     * @return $this
     */
    public function controls(callable $call)
    {
        $this->custom_controls = $call;

        return $this;
    }

    /**
     * @param  Closure|array|null  $test
     * @return $this
     */
    public function disableControls($test = null)
    {
        $this->controls = is_embedded_call($test) ? $test : static function () {
            return false;
        };

        return $this;
    }

    /**
     * @param  Closure|array|null  $test
     * @return $this
     */
    public function disableInfo($test = null)
    {
        $this->info_control = is_embedded_call($test) ? $test : static function () {
            return false;
        };

        return $this;
    }

    /**
     * @param  Closure|array|null  $test
     * @return $this
     */
    public function disableEdit($test = null)
    {
        $this->edit_control = is_embedded_call($test) ? $test : static function () {
            return false;
        };

        return $this;
    }

    /**
     * @param  Closure|array|null  $test
     * @return $this
     */
    public function disableDelete($test = null)
    {
        $this->delete_control = is_embedded_call($test) ? $test : static function () {
            return false;
        };

        return $this;
    }

    protected function mount()
    {
        $model = null;

        if ($this->model instanceof Relation) {
            $model = $this->model->getQuery()->getModel();
        } elseif ($this->model instanceof Builder) {
            $model = $this->model->getModel();
        } elseif ($this->model instanceof Model) {
            $model = $this->model;
        }

        $hasOrder = false;

        if ($model) {
            $fillable = $model->getFillable();
            if (!in_array($this->parent_field, $fillable)) {
                $this->maxDepth = 1;
            }
            if (in_array($this->order_by_field, $fillable)) {
                $hasOrder = true;
            }
            $this->setDatas(['model' => get_class($model)]);
        } else {
            $this->maxDepth = 1;
        }

        $this->setDatas(['max-depth' => $this->maxDepth, 'parent' => $this->parent_field]);

        if ($hasOrder) {
            $this->model = $this->model->orderBy($this->order_by_field, $this->order_by_type);
        }

        $this->model = $this->model->get();

        $this->makeList($this->maxDepth > 1 ? $this->model->whereNull($this->parent_field) : $this->model, $this);
    }

    /**
     * @param  Collection  $model
     * @param  Component  $object
     */
    protected function makeList(Collection $model, \Lar\Layout\Abstracts\Component $object)
    {
        $this->attr('data-order-field', $this->order_by_field);

        $object->ol(['dd-list'])->when(function (OL $ol) use ($model) {
            foreach ($model as $item) {
                $this->makeItem($ol, $item);
            }
        });
    }

    /**
     * @param  Component  $object
     * @param  Model  $item
     */
    protected function makeItem(\Lar\Layout\Abstracts\Component $object, Model $item)
    {
        $object->li(['dd-item dd3-item'])->setDatas(['id' => $item->id])->when(function (LI $li) use ($item) {
            $li->div(['dd-handle dd3-handle'])->when(static function (DIV $div) use ($item) {
                $div->i(['class' => 'fas fa-arrows-alt']);
            });
            $cc_access = ($this->controls)($item);
            $cc = $this->custom_controls;
            if ($cc_access || $cc) {
                $li->div(['float-right m-1'])
                    ->appEndIf(
                        $this->menu,
                        ButtonsComponent::create()->when(function (ButtonsComponent $group) use (
                            $item,
                            $cc_access,
                            $cc
                        ) {
                            $model = $item;
                            $key = $model->getRouteKey();

                            if ($cc) {
                                call_user_func($cc, $group, $model);
                            }

                            if ($cc_access) {
                                if (($this->edit_control)($item)) {
                                    $group->resourceEdit($this->menu['link.edit']($key), '');
                                }

                                if (($this->delete_control)($item)) {
                                    $group->resourceDestroy(
                                        $this->menu['link.destroy']($key),
                                        '',
                                        $model->getRouteKeyName(),
                                        $key
                                    );
                                }

                                if (($this->info_control)($item)) {
                                    $group->resourceInfo($this->menu['link.show']($key), '');
                                }
                            }
                        })
                    );
            }
            $li->div(['dd3-content', 'style' => 'height: auto;min-height: 41px;'])->when(function (DIV $div) use (
                $item
            ) {
                if (is_array($this->title_field)) {
                    //dd($this->model);
                    $tag = Tag::create()->addClass('text')
                        ->newExplainForce($this->title_field)->model($item);
                    $div->appEnd($tag);
                } else {
                    if (is_callable($this->title_field)) {
                        $div->span(['text'])->text(call_user_func($this->title_field, $item));
                    } else {
                        $ddd = multi_dot_call($item, $this->title_field);
                        $div->span(['text'])->text(__(e($ddd)));
                    }
                }
            });
            if ($this->maxDepth > 1) {
                $list = $this->model->where($this->parent_field, $item->id);
                if ($list->count()) {
                    /** @var Collection $list */
                    $this->makeList($list, $li);
                }
            }
        });
    }
}
