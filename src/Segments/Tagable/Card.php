<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\Traits\FontAwesome;
use Lar\LteAdmin\Segments\Tagable\Cores\CoreModelTable;
use Lar\LteAdmin\Segments\Tagable\Traits\TypesTrait;
use Lar\Tagable\Events\onRender;

/**
 * Class Col
 * @package Lar\LteAdmin\Segments\Tagable
 */
class Card extends DIV implements onRender {

    use TypesTrait, FontAwesome;

    /**
     * @var array
     */
    protected $props = [
        'card', 'card-outline'
    ];

    /**
     * @var array|\Lar\LteAdmin\Getters\Menu|null
     */
    protected $now;

    /**
     * @var DIV
     */
    protected $head_obj;

    /**
     * @var \Lar\Layout\Tags\H3
     */
    protected $title_obj;

    /**
     * @var bool
     */
    protected $auto_tools = false;

    /**
     * @var ButtonGroup
     */
    protected $group;

    /**
     * @var DIV
     */
    protected $tools;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var ModelTable
     */
    protected $table;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var string|array
     */
    protected $title;

    /**
     * Card constructor.
     * @param $title
     * @param  mixed  ...$params
     */
    public function __construct($title = null, ...$params)
    {
        $this->type = "primary";

        parent::__construct();

        if ($title instanceof \Closure) {

            $params[] = $title;

        } else if ($title) {

            $this->title = $title;

            $this->head_obj = $this->div(['card-header']);

            $this->title_obj = $this->head_obj->h3(['card-title']);

            $this->tools = $this->head_obj->div(['card-tools']);
        }

        $this->when($params);

        $this->now = gets()->lte->menu->now;

        $this->group = new ButtonGroup();
    }

    /**
     * @param  \Closure  $closure
     * @param  mixed  ...$params
     * @return DIV
     */
    public function body(\Closure $closure = null, ...$params)
    {
        $body = $this->div(['card-body'], ...$params);

        if ($closure) {
            
            $closure($body, $this);
        }
        return $body;
    }

    /**
     * @param  null  $closure
     * @param  mixed  ...$params
     * @return DIV
     */
    public function foolBody(\Closure $closure = null, ...$params)
    {
        $body = $this->div(['card-body p-0'], ...$params);

        if ($closure) {

            $closure($body, $this);
        }

        return $body;
    }

    /**
     * @param  null  $model
     * @param  \Closure|null  $after
     * @return ModelTable
     */
    public function bodyModelTable($model = null, \Closure $after = null)
    {
        $this->table = $this->body(['p-0'])->model_table($model, $after);

        $this->table->table_rendered(function (CoreModelTable $table) {
            $this->bottom_content->add($table->footer());
        });

        return $this->table;
    }

    /**
     * @param  mixed  ...$params
     * @return Card
     */
    public function bodyForm(...$params)
    {
        $this->form = $this->body()->form(...$params);

        return $this;
    }

    /**
     * @param  mixed  ...$params
     * @return DIV
     */
    public function footer(...$params)
    {
        return $this->div(['card-footer'], ...$params);
    }

    /**
     * @param  mixed  ...$params
     * @return $this
     */
    public function footerForm(...$params)
    {
        $this->div(['card-footer'])->appEnd(FormFooter::create(...$params));

        return $this;
    }

    /**
     * @return $this
     */
    public function defaultTools()
    {
        if ($this->now['current.type']) {

            $type = $this->now['current.type'];

            $this->group->reload();

            if ($type === 'create') {

                $this->group->resourceList();
            }

            else if ($type === 'edit' || $type === 'show') {

                $this->group->resourceList();

                if ($type === 'show') {

                    $this->group->resourceEdit();
                }

                if ($type === 'edit') {

                    $this->group->resourceInfo();
                }

                $this->group->resourceDestroy();
            }

            if ($type !== 'create') {

                $this->group->resourceAdd();
            }
        }

        return $this;
    }

    /**
     * @param  mixed  ...$params
     * @return ButtonGroup
     */
    public function tools(...$params)
    {
        return $this->group;
    }

    /**
     * @param  mixed  ...$params
     * @return ButtonGroup
     */
    public function group(...$params)
    {
        $group = ButtonGroup::create(...$params);

        $this->tools->appEnd($group);

        return $group;
    }

    /**
     * @return mixed|void
     */
    public function onRender()
    {
        $this->addClass("card-{$this->type}");

        $model = gets()->lte->menu->model;

        if ($this->title_obj) {

            if ($this->icon) {

                $this->title_obj->text("<i class=\"{$this->icon} mr-1\"></i>");
            }

            if ($model && is_array($this->title)) {

                foreach ($this->title as $key => $attr) {

                    if (is_string($attr)) {

                        $this->title[$key] = multi_dot_call($model, $attr) ?? $attr;
                    }
                }
            }

            $this->title_obj->text(is_array($this->title) ? implode(" ", array_map('__', $this->title)) : __($this->title));
        }

        if ($this->tools) {

            $this->tools->appEnd($this->group);
        }
    }

    /**
     * @param  string  $icon
     * @return $this
     */
    public function icon(string $icon)
    {
        $this->icon = $icon;

        return $this;
    }
}