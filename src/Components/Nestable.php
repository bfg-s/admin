<?php

namespace Lar\LteAdmin\Components;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\LI;
use Lar\Layout\Tags\OL;

class Nestable extends DIV
{
    /**
     * @var string[]
     */
    protected $props = [
        'dd'
    ];

    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\Relation|\Lar\LteAdmin\Getters\Menu|string|null
     */
    protected $model;

    /**
     * @var array|\Lar\LteAdmin\Getters\Menu|null
     */
    protected $menu;

    /**
     * @var string
     */
    protected $title_field = "title";

    /**
     * @var string
     */
    protected $parent_field = "parent_id";

    /**
     * Shoe default controls
     *
     * @var bool
     */
    protected $controls = true;

    /**
     * @var bool
     */
    protected $info_control = true;

    /**
     * @var bool
     */
    protected $delete_control = true;

    /**
     * @var bool
     */
    protected $edit_control = true;

    /**
     * @var string
     */
    private $order_by_field = "order";

    /**
     * @var string
     */
    private $order_by_type = "asc";

    /**
     * @var int
     */
    private $maxDepth = 5;

    /**
     * Col constructor.
     * @param  null  $model
     * @param  array|null  $instructions
     * @param  mixed  ...$params
     */
    public function __construct($model = null, array $instructions = [], ...$params)
    {
        if (is_array($model)) {

            $instructions = $model;
            $model = null;
        }

        if (!$model) {

            $model = gets()->lte->menu->model;
        }

        $this->model = eloquent_instruction($model, $instructions);

        $this->menu = gets()->lte->menu->now;

        parent::__construct();

        $this->setDatas(['load' => 'nestable']);

        $this->when($params);
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
     * Build nestable
     * @return $this
     */
    public function build()
    {
        $model = null;

        if ($this->model instanceof Relation) {

            $model = $this->model->getQuery()->getModel();
        }

        else if ($this->model instanceof Builder) {

            $model = $this->model->getModel();
        }

        else if ($this->model instanceof Model) {

            $model = $this->model;
        }

        if ($model) {

            if (array_search($this->parent_field, $model->getFillable()) === false) {

                $this->maxDepth = 1;
            }

            $this->setDatas(['model' => get_class($model)]);
        }

        else {

            $this->maxDepth = 1;
        }

        $this->setDatas(['max-depth' => $this->maxDepth, 'parent' => $this->parent_field]);

        $this->model = $this->model->orderBy($this->order_by_field, $this->order_by_type)->get();

        $this->makeList($this->maxDepth > 1 ? $this->model->whereNull($this->parent_field) : $this->model, $this);

        return $this;
    }

    /**
     * @param  Collection|Model[]  $model
     * @param  Component  $object
     */
    protected function makeList(Collection $model, Component $object)
    {
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
    protected function makeItem(Component $object, Model $item)
    {
        $object->li(['dd-item dd3-item'])->setDatas(['id' => $item->id])->when(function (LI $li) use ($item) {
            $li->div(['dd-handle dd3-handle'])->when(function (DIV $div) use ($item) {
                $div->i(['class' => isset($item->icon) && !empty($item->icon) ? $item->icon : 'fas fa-arrows-alt']);
            });
            $li->div(['dd3-content'])->when(function (DIV $div) use ($item) {
                $div->span(['text'])->text(multi_dot_call($item, $this->title_field));
                if($this->controls) {
                    $div->div(['float-right'])
                        ->appEndIf($this->menu, ButtonGroup::create(function (ButtonGroup $group) use ($item) {
                        $action = \Str::before(\Route::currentRouteAction(), '@');

                        $rk_name = $item->getRouteKeyName();

                        $key = $item->getOriginal($rk_name);

                        if ($this->edit_control && $key && isset($this->menu['link.edit']) && (method_exists($action, 'edit') || method_exists($action, 'edit_default'))) {

                            $group->success('fas fa-edit')->setTitle(__('lte::admin.edit'))->dataClick()->location(
                                $this->menu['link.edit']([$this->menu['model.param'] => $key])
                            );
                        }

                        if ($this->delete_control && $key && isset($this->menu['link.destroy']) && (method_exists($action, 'destroy') || method_exists($action, 'destroy_default'))) {

                            $group->danger('fas fa-trash-alt')
                                ->setTitle(__('lte::admin.delete'))->setDatas([
                                    'click' => 'alert::confirm',
                                    'params' => [
                                        __('lte::admin.delete_subject', ['subject' => strtoupper($rk_name).":{$key}?"]),
                                        $this->menu['link.destroy']([$this->menu['model.param'] => $key]) . " >> \$jax.del"
                                    ]
                                ]);
                        }

                        if ($this->info_control && $key && isset($this->menu['link.show']) && (method_exists($action, 'show') || method_exists($action, 'show_default'))) {

                            $group->info('fas fa-info-circle')->setTitle(__('lte::admin.information'))->dataClick()->location(
                                $this->menu['link.show']([$this->menu['model.param'] => $key])
                            );
                        }
                    }));
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

    /**
     * @param  string  $field
     * @return $this
     */
    public function title_field(string $field)
    {
        $this->title_field = $field;

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
     * @return $this
     */
    public function disableControls()
    {
        $this->controls = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableInfo()
    {
        $this->info_control = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableEdit()
    {
        $this->edit_control = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableDelete()
    {
        $this->delete_control = false;

        return $this;
    }
}