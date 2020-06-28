<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Lar\Layout\Tags\DIV;
use Lar\Layout\Traits\FontAwesome;
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
     * @var DIV
     */
    protected $body;

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
     * @var bool
     */
    protected $default_tools = false;

    /**
     * @var SearchForm
     */
    protected $search_form;

    /**
     * @var bool
     */
    protected $has_search_form = false;

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

        $this->callConstructEvents();
    }

    /**
     * @param  \Closure  $closure
     * @return $this
     */
    public function search(\Closure $closure)
    {
        $this->has_search_form = true;

        $closure($this->search_form);

        return $this;
    }

    /**
     * @param  mixed  ...$params
     * @return DIV
     */
    public function body(...$params)
    {
        return $this->div(['card-body'], ...$params)
            ->haveLink($this->body);
    }

    /**
     * @return DIV
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param  mixed  ...$params
     * @return DIV
     */
    public function foolBody(...$params)
    {
        return $this->div(['card-body p-0'], ...$params);
    }

    /**
     * @param  null  $model
     * @param  \Closure|null  $after
     * @return ModelTable
     */
    public function bodyModelTable($model = null, \Closure $after = null)
    {
        $this->search_form = new SearchForm();

        $this->body($this->search_form)->setId("table_search_form")->setStyle("display: none");

        $this->table = $this->body(['p-0', 'table-responsive'])
            ->model_table($model, $after);
        
        $this->table->model($this->search_form);

        $this->table->rendered(function (ModelTable $table) {
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
     * @param  \Closure|null  $test
     * @return $this
     */
    public function defaultTools(\Closure $test = null)
    {
        $this->default_tools = $test ? $test : function () { return true; };

        return $this;
    }

    /**
     * @return $this
     */
    public function nestedTools()
    {
        $this->group()->nestable();

        return $this;
    }

    /**
     * @param  mixed  ...$params
     * @return ButtonGroup
     */
    public function tools()
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
        $this->callRenderEvents();

        $this->make_default_tools();
        
        $this->addClass("card-{$this->type}");

        $model = gets()->lte->menu->model;

        if ($this->title_obj) {

            if ($this->icon) {

                $this->title_obj->text("<i class=\"{$this->icon} mr-1\"></i>");
            }

            $this->title_obj->text(preg_replace_callback('/\:([a-zA-Z0-9\_\-\.]+)/', function ($m) use ($model) {
                return multi_dot_call($model, $m[1]);
            }, __($this->title)));
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

    protected function make_default_tools()
    {
        if ($this->default_tools !== false) {

            /** @var \Closure $test */
            $test = $this->default_tools;

            if ($this->has_search_form && $this->now['current.type'] && $this->now['current.type'] === 'index') {

                if ($test('search')) {
                    $this->group(function (ButtonGroup $group) {

                        $group->primary(['fas fa-search', __('lte.search')])
                            ->on_click('lte::switch_search');

                        if (request()->has('q')) {
                            $group->danger(['fas fa-window-close', __('lte.cancel')])
                                ->on_click('doc::location', urlWithGet([], ['q']));
                        }
                    });
                }
            }

            if ($test('reload')) {
                $this->group->reload();
            }

            if ($this->now['current.type']) {

                $type = $this->now['current.type'];

                if ($type === 'create') {

                    if ($test('list')) {
                        $this->group->resourceList();
                    }
                }

                else if ($type === 'edit' || $type === 'show') {

                    if ($test('list')) {
                        $this->group->resourceList();
                    }

                    if ($type === 'show') {

                        if ($test('edit')) {
                            $this->group->resourceEdit();
                        }
                    }

                    if ($type === 'edit') {

                        if ($test('info')) {
                            $this->group->resourceInfo();
                        }
                    }

                    if ($test('delete')) {
                        $this->group->resourceDestroy();
                    }
                }

                if ($type !== 'create') {

                    if ($test('add')) {
                        $this->group->resourceAdd();
                    }
                }
            }
        }
    }
}