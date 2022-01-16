<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\BUTTON;
use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\H3;
use Lar\Layout\Traits\FontAwesome;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Segments\Tagable\Traits\TypesTrait;
use Lar\Tagable\Events\onRender;

/**
 * Class Col
 * @package Lar\LteAdmin\Segments\Tagable
 * @mixin CardMacroList
 */
class Card extends DIV implements onRender {

    use TypesTrait, FontAwesome, Macroable, Delegable;

    static $isContainer = true;

    /**
     * @var SearchForm
     */
    public $search_form;

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
     * @var CardBody
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
     * @var bool
     */
    protected $has_search_form = true;

    /**
     * Card constructor.
     * @param \Closure|array|string|null $title
     * @param  mixed  ...$params
     */
    public function __construct($title = null, ...$params)
    {
        $this->type = "primary";

        parent::__construct();

        if (is_embedded_call($title)) {

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
     * @param  callable  $call
     * @return $this
     */
    public function headerObj(callable $call)
    {
        call_user_func($call, $this->head_obj);
        return $this;
    }

    /**
     * @param  callable  $call
     * @return $this
     */
    public function titleObj(callable $call)
    {
        call_user_func($call, $this->title_obj);
        return $this;
    }

    /**
     * @param  callable  $call
     * @return $this
     */
    public function toolsObj(callable $call)
    {
        call_user_func($call, $this->tools);
        return $this;
    }

    /**
     * @param  mixed  ...$params
     * @return CardBody
     */
    public function body(...$params)
    {
        $body = CardBody::create(...$params)->haveLink($this->body);
        $this->appEnd($body);
        return $body;
    }

    /**
     * @return CardBody
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param  mixed  ...$params
     * @return CardBody
     */
    public function fullBody(...$params)
    {
        return $this->body(['p-0'], ...$params);
    }

    /**
     * @return $this
     */
    public function withSearchForm()
    {
        if (!$this->search_form) {

            $this->search_form = new SearchForm();

            $this->div(['#table_search_form', 'collapse'])
                ->div(['card-body'], $this->search_form);
        }

        return $this;
    }

    /**
     * @param  null  $model
     * @param  \Closure|array|null  $after
     * @return ModelTable
     */
    public function bodyModelTable($model = null, $after = null)
    {
        $this->withSearchForm();

        $this->table = $this->body(['p-0', 'table-responsive'])
            ->model_table($model, function (ModelTable $table) {
                $table->model($this->search_form);
            }, $after);

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
        $this->div(['card-footer'])->appEnd(FormFooter::create(...$params)->createDefaultCRUDFooter());

        return $this;
    }

    /**
     * @param  \Closure|array|null  $test
     * @return $this
     */
    public function defaultTools($test = null)
    {
        $this->default_tools = is_embedded_call($test) ? $test : function () { return true; };

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

        if ($this->tools) {

            $this->tools->appEnd($group);
        }


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

    /**
     * Make default tools
     */
    protected function make_default_tools()
    {
        if ($this->default_tools !== false) {

            /** @var \Closure $test */
            $test = $this->default_tools;

            if ($test('search') && lte_controller_can('search')) {
                $this->group(function (ButtonGroup $group) {

                    $group->primary(['fas fa-search', __('lte.search')])
                        ->setDatas([
                            'toggle' => 'collapse',
                            'target' => '#table_search_form'
                        ])->attr([
                            'aria-expanded' => 'true',
                            'aria-controls' =>  'table_search_form'
                        ])->whenRender(function (BUTTON $button) {
                            if (!$this->search_form || !$this->search_form->fieldsCount()) {
                                $button->attr(['d-none']);
                            }
                        });

                    if ($this->search_form && request()->has('q')) {
                        $group->danger(['fas fa-window-close', __('lte.cancel')])
                            ->attr('id', 'cancel_search_params')
                            ->on_click('doc::location', urlWithGet([], ['q', 'page']))
                            ->whenRender(function (BUTTON $button) {
                                if (!$this->search_form || !$this->search_form->fieldsCount()) {
                                    $button->attr(['d-none']);
                                }
                            });
                    }
                });
            }

            if ($this->has_search_form && $this->now['current.type'] && $this->now['current.type'] === 'index') {

                /** @var Model $model */
                $model = gets()->lte->menu->model;

                if ($model && property_exists($model, 'forceDeleting')) {

                    if (!request()->has('show_deleted')) {
                        $this->group()->dark('fas fa-trash')
                            ->on_click('doc::location', urlWithGet(['show_deleted' => 1]));
                    } else {
                        $this->group()->resourceList(urlWithGet([], ['show_deleted']));
                    }
                }
            }

            if ($this->now['current.type'] && !request()->has('show_deleted')) {

                $type = $this->now['current.type'];

                if ($type === 'create') {

                    if ($test('list') && lte_controller_can('index')) {
                        $this->group->resourceList();
                    }
                }

                else if ($type === 'edit' || $type === 'show') {

                    if ($test('list') && lte_controller_can('index')) {
                        $this->group->resourceList();
                    }

                    if ($type === 'show') {

                        if ($test('edit') && lte_controller_can('edit')) {
                            $this->group->resourceEdit();
                        }
                    }

                    if ($type === 'edit') {

                        if ($test('info') && lte_controller_can('show')) {
                            $this->group->resourceInfo();
                        }
                    }

                    if ($test('delete') && lte_controller_can('destroy')) {
                        $this->group->resourceDestroy();
                    }
                }

                if ($type !== 'create') {

                    if ($test('add') && lte_controller_can('create')) {
                        $this->group->resourceAdd();
                    }
                }
            }
        }
    }
}
