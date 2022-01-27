<?php

namespace Lar\LteAdmin\Components;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Lar\Layout\LarDoc;
use Lar\Layout\Tags\BUTTON;
use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\H3;
use Lar\Layout\Traits\FontAwesome;
use Lar\LteAdmin\Components\Traits\TypesTrait;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Explanation;
use Lar\LteAdmin\Getters\Menu;
use Lar\LteAdmin\Page;

class CardComponent extends Component
{
    use TypesTrait, FontAwesome, Macroable, Delegable;

    public static $isContainer = true;

    /**
     * @var SearchFormComponent
     */
    public $search_form;

    /**
     * @var array
     */
    protected $props = [
        'card', 'card-outline', 'w-100',
    ];

    /**
     * @var array|Menu|null
     */
    protected $now;

    /**
     * @var DIV
     */
    protected $head_obj;

    /**
     * @var H3
     */
    protected $title_obj;

    /**
     * @var ButtonsComponent
     */
    protected $group;

    /**
     * @var DIV
     */
    protected $tools;

    /**
     * @var FormComponent
     */
    protected $form;

    /**
     * @var CardBodyComponent
     */
    protected $body;

    /**
     * @var ModelTableComponent
     */
    protected $table;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var string|array
     */
    protected $title = null;

    /**
     * @var bool
     */
    protected $default_tools = false;

    /**
     * @var bool
     */
    protected $has_search_form = true;
    protected Page $page;

    /**
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        $this->type = 'primary';

        parent::__construct();

        $this->head_obj = $this->div(['card-header']);

        $this->title_obj = $this->head_obj->h3(['card-title']);

        $this->tools = $this->head_obj->div(['card-tools']);

        $this->now = $this->menu;

        $this->group = new ButtonsComponent();

        $this->explainForce(Explanation::new($delegates));
    }

    public static function registrationInToContainer(Page $page, array $delegates = [])
    {
        $page->registerClass($page->next()->getContent()->card($delegates));
    }

    public function tab(...$delegates)
    {
        if (!$this->body) {
            $this->fullBody();
        }

        array_unshift($delegates, TabContentComponent::new()->p3()->pr4());
        $this->body->tab($delegates);

        return $this;
    }

    /**
     * @param ...$params
     * @return \Lar\Layout\Abstracts\Component|LarDoc|CardBodyComponent
     */
    public function fullBody(...$params)
    {
        return $this->card_body()->addClass('p-0')->when($params);
    }

    /**
     * @param ...$delegations
     * @return \Lar\Layout\Abstracts\Component|LarDoc|CardBodyComponent
     */
    public function card_body(...$delegations)
    {
        $body = CardBodyComponent::create($delegations)->haveLink($this->body);
        $this->appEnd($body);

        return $body;
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
     * @param  array|string  $title
     * @return $this
     */
    public function title(array|string $title)
    {
        $this->title = $title;

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
     * @return CardBodyComponent
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return $this
     */
    public function search_form(...$delegates)
    {
        if (!$this->search_form) {
            $this->search_form = new SearchFormComponent(...$delegates);

            $this->div(['.table_search_form', 'collapse'])
                ->div(['card-body'], $this->search_form);
            if (request()->has('q') && $component = $this->search_form->getSearchInfoComponent()) {
                $this->div(['.table_search_form', 'collapse', 'show'])
                    ->div(['card-body p-0'], $component);
            }
        }

        return $this;
    }

    /**
     * @param ...$delegates
     * @return ModelTableComponent
     */
    public function model_table(...$delegates)
    {
        $this->table = $this->card_body()->attr(['p-0', 'table-responsive'])
            ->model_table($delegates)->model($this->search_form);

        $this->table->rendered(function (ModelTableComponent $table) {
            $this->bottom_content->add($table->footer());
        });

        $this->headerObj(function (DIV $div) {
            $ad = $this->table->getActionData();
            if ($ad['show']) {
                $div->prepEnd()->view('lte::segment.model_table_actions', $ad);
            }
        });

        return $this->table;
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

    public function model_info_table(...$delegates)
    {
        return $this->fullBody()->model_info_table($delegates);
    }

    /**
     * @param ...$delegates
     * @return FormComponent
     */
    public function form(...$delegates)
    {
        return $this->card_body()->form(...$delegates);
    }

    /**
     * @param ...$delegates
     * @return NestedComponent
     */
    public function nested(...$delegates)
    {
        return $this->card_body()->nested(...$delegates)->model($this->search_form);
    }

    /**
     * @param  mixed  ...$params
     * @return DIV
     */
    public function footer(...$params)
    {
        return $this->div(['card-footer row'], ...$params);
    }

    /**
     * @param ...$params
     * @return \Lar\Layout\Abstracts\Component|LarDoc|FormFooterComponent
     */
    public function footer_form(...$delegates)
    {
        $footer = FormFooterComponent::create(...$delegates)->createDefaultCRUDFooter();
        $this->div(['card-footer'])->appEnd($footer);

        return $footer;
    }

    /**
     * @param  Closure|array|null  $test
     * @return $this
     */
    public function defaultTools($test = null)
    {
        $this->default_tools = is_embedded_call($test) ? $test : static function () {
            return true;
        };

        return $this;
    }

    /**
     * @return $this
     */
    public function nestedTools()
    {
        $this->buttons()->nestable();

        return $this;
    }

    /**
     * @param  mixed  ...$params
     * @return ButtonsComponent
     */
    public function buttons(...$params)
    {
        $group = ButtonsComponent::create()->when($params);

        if ($this->tools) {
            $this->tools->appEnd($group);
        }

        return $group;
    }

    /**
     * @return ButtonsComponent
     */
    public function tools()
    {
        return $this->group;
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

    protected function mount()
    {
        $this->callRenderEvents();

        $this->make_default_tools();

        $this->addClass("card-{$this->type}");

        $model = gets()->lte->menu->model;

        $originTitle = $this->title;
        $this->title = is_array($this->title) && isset($this->title[0]) ? $this->title[0] : $this->title;
        if (lte_model_type('index')) {
            $this->title = $this->title !== null ? $this->title : 'lte.list';
            if (request()->has('show_deleted') && request('show_deleted') == 1) {
                $this->title = __($this->title).' <small><b>('.__('lte.deleted').')</b></small>';
            }
        } elseif (lte_model_type('create')) {
            $this->title = $this->title !== null ? $this->title : 'lte.add';
        } elseif (lte_model_type('edit')) {
            $this->title = is_array($originTitle) && isset($originTitle[1]) ? $originTitle[1] : $this->title;
            $this->title = $this->title !== null ? $this->title : 'lte.id_edit';
        } elseif (lte_model_type('show')) {
            $this->title = $this->title !== null ? $this->title : 'lte.information';
        }

        if ($this->title_obj) {
            if ($this->icon) {
                $this->title_obj->text("<i class=\"{$this->icon} mr-1\"></i>");
            }

            $this->title_obj->text(preg_replace_callback('/\:([a-zA-Z0-9\_\-\.]+)/', static function ($m) use ($model) {
                return e(multi_dot_call($model, $m[1]));
            }, __($this->title)));
        }

        if ($this->tools) {
            $this->tools->appEnd($this->group);
        }
    }

    /**
     * Make default tools.
     */
    protected function make_default_tools()
    {
        if ($this->default_tools !== false) {
            /** @var Closure $test */
            $test = $this->default_tools;

            if ($test('search')) {
                $this->buttons(function (ButtonsComponent $group) {
                    $group->primary(['fas fa-search', __('lte.search')])
                        ->setDatas([
                            'toggle' => 'collapse',
                            'target' => '.table_search_form',
                        ])->attr([
                            'aria-expanded' => 'true',
                            'aria-controls' => 'table_search_form',
                        ])->whenRender(function (BUTTON $button) {
                            if (!$this->search_form || !$this->search_form->fieldsCount()) {
                                $button->attr(['d-none']);
                            }
                        });

                    if ($this->search_form && request()->has('q')) {
                        $group->danger(['fas fa-window-close', __('lte.cancel')])
                            ->attr('id', 'cancel_search_params')
                            ->location([], ['q', 'page'])
                            ->whenRender(function (BUTTON $button) {
                                if (!$this->search_form || !$this->search_form->fieldsCount()) {
                                    $button->attr(['d-none']);
                                }
                            });
                    }
                });
            }

            if ($this->has_search_form && $this->now && $this->now['current.type'] && $this->now['current.type'] === 'index') {
                /** @var Model $model */
                $model = gets()->lte->menu->model;

                if ($model && property_exists($model, 'forceDeleting')) {
                    if (!request()->has('show_deleted')) {
                        $this->buttons()->dark('fas fa-trash')
                            ->on_click('doc::location', urlWithGet(['show_deleted' => 1]))->setTitle(__('lte.deleted'));
                    } else {
                        $this->buttons()->resourceList(urlWithGet([], ['show_deleted']));
                    }
                }
            }

            if ($this->now && $this->now['current.type'] && !request()->has('show_deleted')) {
                $type = $this->now['current.type'];

                if ($type === 'create') {
                    if ($test('list')) {
                        $this->group->resourceList();
                    }
                } elseif ($type === 'edit' || $type === 'show') {
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
