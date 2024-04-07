<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Models\AdminPermission;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Admin\Core\MenuItem;
use Admin\Explanation;
use Admin\Page;
use Admin\Traits\Delegable;
use Admin\Traits\FontAwesome;
use Admin\Traits\TypesTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class CardComponent extends Component
{
    use TypesTrait;
    use FontAwesome;
    use Delegable;

    /**
     * @var bool
     */
    public static bool $isContainer = true;

    /**
     * @var SearchFormComponent|null
     */
    public ?SearchFormComponent $search_form = null;

    /**
     * @var MenuItem|null
     */
    protected ?MenuItem $now = null;

    /**
     * @var CardBodyComponent|null
     */
    protected ?CardBodyComponent $body = null;

    /**
     * @var ModelTableComponent|null
     */
    protected ?ModelTableComponent $table = null;

    /**
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * @var string|array|null
     */
    protected string|array|null $title = null;

    /**
     * @var mixed
     */
    protected mixed $default_tools = false;

    /**
     * @var bool
     */
    protected bool $window_controls = true;

    /**
     * @var bool
     */
    protected bool $has_search_form = true;

    /**
     * @var Page
     */
    protected Page $page;

    /**
     * @var string
     */
    protected string $view = 'card';

    /**
     * @var array
     */
    protected array $groups = [];

    /**
     * Addition header object
     * @var View|null
     */
    protected ?View $headerObj = null;

    /**
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        $this->type = 'primary';

        parent::__construct();

        $this->now = $this->menu;

        $this->groups[] = new ButtonsComponent();

        $this->explainForce(Explanation::new($delegates));
    }

    /**
     * @param  Page  $page
     * @param  array  $delegates
     * @return void
     */
    public static function registrationInToContainer(Page $page, array $delegates = []): void
    {
        $page->registerClass($page->next()->getContent()->card($delegates));
    }

    /**
     * @param ...$delegates
     * @return $this
     */
    public function tab(...$delegates): static
    {
        if (!$this->body) {
            $this->full_body();
        }

        array_unshift($delegates, TabContentComponent::new()->p3()->pr4());
        $this->body->tab($delegates);

        return $this;
    }

    /**
     * @param ...$params
     * @return CardBodyComponent
     */
    public function full_body(...$params): CardBodyComponent
    {
        return $this->card_body()->fullSpace()->use($params);
    }

    /**
     * @param ...$delegations
     * @return CardBodyComponent
     */
    public function card_body(...$delegations): CardBodyComponent
    {
        $body = $this->body = $this->createComponent(CardBodyComponent::class, $delegations)
            ->model($this->realModel());

        $this->appEnd($body);

        return $body;
    }

    /**
     * @param  array|string  $title
     * @return $this
     */
    public function title(array|string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return CardBodyComponent|null
     */
    public function getBody(): ?CardBodyComponent
    {
        return $this->body;
    }

    /**
     * @return $this
     */
    public function search_form(...$delegates): static
    {
        if (!$this->search_form) {
            $this->search_form = new SearchFormComponent(...$delegates);

            $hasQ = request()->has('q');

            $this->view('components.card.search-form-body', [
                'content' => $this->search_form,
                'hasQ' => $hasQ,
                'searchInfo' => $hasQ ? $this->search_form->getSearchInfoComponent() : null,
            ]);
        }

        return $this;
    }

    /**
     * @param ...$delegates
     * @return ModelTableComponent
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function model_table(...$delegates): ModelTableComponent
    {
        $body = $this->card_body()->tableResponsive()->fullSpace();
        $this->table = $body->model_table($delegates);

        $this->table->model($this->search_form);

        $ad = $this->table->getActionData();

        if ($ad['show']) {

            $this->headerObj = admin_view('components.model-table.actions', $ad);
        }

        return $this->table;
    }

    /**
     * @param ...$delegates
     * @return ChartJsComponent
     */
    public function chart_js(...$delegates): ChartJsComponent
    {
        return $this->card_body()->chart_js(...$delegates)
            ->model($this->search_form);
    }

    /**
     * @param ...$delegates
     * @return ModelInfoTableComponent
     */
    public function model_info_table(...$delegates): ModelInfoTableComponent
    {
        return $this->full_body()->model_info_table($delegates);
    }

    /**
     * @param ...$delegates
     * @return FormComponent
     */
    public function form(...$delegates): FormComponent
    {
        return $this->card_body()->form(...$delegates);
    }

    /**
     * @param ...$delegates
     * @return NestedComponent
     */
    public function nested(...$delegates): NestedComponent
    {
        return $this->card_body()->nested(...$delegates)->model($this->search_form);
    }

    /**
     * @param  mixed  ...$params
     * @return FormFooterComponent
     */
    public function footer(...$params): FormFooterComponent
    {
        return $this->createComponent(FormFooterComponent::class, ...$params)
            ->setRow(true);
    }

    /**
     * @param  mixed  ...$delegates
     * @return FormFooterComponent
     */
    public function footer_form(...$delegates): FormFooterComponent
    {
        $footer = $this->createComponent(FormFooterComponent::class, ...$delegates)
            ->createDefaultCRUDFooter();

        $this->appEnd($footer);

        return $footer;
    }

    /**
     * @param  array|Closure|null  $test
     * @return $this
     */
    public function defaultTools(mixed $test = null): static
    {
        $this->default_tools = is_embedded_call($test) ? $test : static function () {
            return true;
        };

        return $this;
    }

    /**
     * @return $this
     */
    public function nestedTools(): static
    {
        $this->buttons()->nestable();

        return $this;
    }

    /**
     * @param  string  $name
     * @return $this
     */
    public function icon(string $name): static
    {
        $this->icon = $name;

        return $this;
    }

    /**
     * @param  bool  $eq
     * @return $this
     */
    public function windowControls(bool $eq = true): static
    {
        $this->window_controls = $eq;

        return $this;
    }

    /**
     * @return $this
     */
    public function withoutWindowControls(): static
    {
        return $this->windowControls(false);
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
        $this->make_default_tools();

        $originTitle = $this->title;
        $this->title = is_array($this->title) && isset($this->title[0]) ? $this->title[0] : $this->title;
        if (admin_model_type('index')) {
            $this->title = $this->title !== null ? $this->title : 'admin.list';
            if (request()->has('show_deleted') && request('show_deleted') == 1) {
                $this->title = __($this->title).' <small><b>('.__('admin.deleted').')</b></small>';
            }
        } elseif (admin_model_type('create')) {
            $this->title = $this->title !== null ? $this->title : 'admin.add';
        } elseif (admin_model_type('edit')) {
            $this->title = is_array($originTitle) && isset($originTitle[1]) ? $originTitle[1] : $this->title;
            $this->title = $this->title !== null ? $this->title : 'admin.id_edit';
        } elseif (admin_model_type('show')) {
            $this->title = $this->title !== null ? $this->title : 'admin.information';
        }
    }

    /**
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'type' => $this->type,
            'model' => admin_repo()->modelNow,
            'title' => $this->title,
            'icon' => $this->icon,
            'footer' => fn () => $this->table?->footer(),
            'window_controls' => $this->window_controls,
            'groups' => $this->groups,
            'default_tools' => $this->default_tools,
            'search_form' => $this->search_form,
            'headerObj' => $this->headerObj
        ];
    }

    /**
     * @param  mixed  ...$delegates
     * @return ButtonsComponent
     */
    public function buttons(...$delegates): ButtonsComponent
    {
        $this->groups[] = $current = ButtonsComponent::create(...$delegates);

        return $current;
    }

    /**
     * Make default tools.
     * @return void
     */
    protected function make_default_tools(): void
    {
        if ($this->default_tools !== false) {
            /** @var Closure $test */
            $test = $this->default_tools;

            if ($test('search')) {
                $group = $this->buttons();
                $group->primary(['fas fa-search', __('admin.search')])
                    ->setDatas([
                        'toggle' => 'collapse',
                        'target' => '.table_search_form',
                    ])->attr([
                        'aria-expanded' => 'true',
                        'aria-controls' => 'table_search_form',
                    ])->whenRender(function (ButtonComponent $button) {
                        if (!$this->search_form || !$this->search_form->fieldsCount()) {
                            $button->addClass('d-none');
                        }
                    });

                if ($this->search_form && request()->has('q')) {
                    $group->danger(['fas fa-window-close', __('admin.cancel')])
                        ->attr('id', 'cancel_search_params')
                        ->query([], ['q', 'page'])
                        ->whenRender(function (ButtonComponent $button) {
                            if (!$this->search_form || !$this->search_form->fieldsCount()) {
                                $button->addClass('d-none');
                            }
                        });
                }
            }

            if ($this->has_search_form && $this->now && $this->now->isType('index')) {
                /** @var Model $model */
                $model = admin_repo()->modelNow;

                if ($model && property_exists($model, 'forceDeleting')) {
                    if (!request()->has('show_deleted')) {
                        $this->buttons()->dark('fas fa-trash')
                            ->on_click('location', admin_url_with_get(['show_deleted' => 1]))->setTitle(__('admin.deleted'));
                    } else {
                        $this->buttons()->resourceList(admin_url_with_get([], ['show_deleted']));
                    }
                }
            }

            if ($this->now && $this->now->isResource() && !request()->has('show_deleted')) {
                $type = $this->now->getType();

                $btn = $this->buttons();

                if ($type === 'create') {
                    if ($test('list')) {
                        if (AdminPermission::checkUrl($this->menu->getLinkIndex(), 'GET')) {
                            $btn->resourceList($this->menu->getLinkIndex());
                        }
                    }
                } elseif ($type === 'edit' || $type === 'show') {

                    $key = $this->realModel()->getRouteKey();

                    if ($test('list')) {
                        if (AdminPermission::checkUrl($this->menu->getLinkIndex(), 'GET')) {
                            $btn->resourceList($this->menu->getLinkIndex());
                        }
                    }

                    if ($type === 'show') {
                        if ($test('edit')) {
                            if (AdminPermission::checkUrl($this->menu->getLinkEdit($key), 'PUT')) {
                                $btn->resourceEdit($this->menu->getLinkEdit($key));
                            }
                        }
                    }

                    if ($type === 'edit') {
                        if ($test('info')) {
                            if (AdminPermission::checkUrl($this->menu->getLinkShow($key), 'GET')) {
                                $btn->resourceInfo($this->menu->getLinkShow($key));
                            }
                        }
                    }

                    if ($test('delete')) {
                        if (AdminPermission::checkUrl($this->menu->getLinkDestroy($key), 'DELETE')) {
                            $btn->resourceDestroy($this->menu->getLinkDestroy($key));
                        }
                    }
                }

                if ($type !== 'create') {
                    if ($test('add')) {
                        if (AdminPermission::checkUrl($this->menu->getLinkCreate(), 'POST')) {
                            $btn->resourceAdd($this->menu->getLinkCreate());
                        }
                    }
                }
            }
        }
    }
}
