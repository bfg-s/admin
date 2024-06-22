<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Core\MenuItem;
use Admin\Delegates\Form;
use Admin\Delegates\Modal;
use Admin\Explanation;
use Admin\Models\AdminPermission;
use Admin\Page;
use Admin\Respond;
use Admin\Traits\FontAwesomeTrait;
use Admin\Traits\Typeable;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * The card component is responsible for all cards in the admin panel.
 */
class CardComponent extends Component
{
    use Typeable;
    use FontAwesomeTrait;

    /**
     * Link to the search form in the map if available
     *
     * @var SearchFormComponent|null
     */
    public SearchFormComponent|null $search_form = null;

    /**
     * Element of the current menu item.
     *
     * @var MenuItem|null
     */
    protected MenuItem|null $nowMenu = null;

    /**
     * Link to the card body class.
     *
     * @var CardBodyComponent|null
     */
    protected CardBodyComponent|null $body = null;

    /**
     * Link to the card table if it exists.
     *
     * @var ModelTableComponent|ModelCardsComponent|null
     */
    protected ModelTableComponent|ModelCardsComponent|null $table = null;

    /**
     * Selected card icon.
     *
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * Selected card title.
     *
     * @var string|array|null
     */
    protected string|array|null $title = null;

    /**
     * Callback to check whether to display standard tools on the card or not.
     *
     * @var mixed
     */
    protected mixed $default_tools = false;

    /**
     * Whether or not to display map control buttons such as maximize, collapse and remove.
     *
     * @var bool
     */
    protected bool $window_controls = true;

    /**
     * The current instance of the page class.
     *
     * @var Page
     */
    protected Page $page;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'card';

    /**
     * All groups of buttons that are present on the card.
     *
     * @var array
     */
    protected array $groups = [];

    /**
     * An additional map header object, used to add actions to the table.
     *
     * @var View|null
     */
    protected ?View $headerObj = null;

    /**
     * CardComponent constructor.
     *
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        $this->type = 'primary';

        parent::__construct();

        $this->nowMenu = $this->menu;

        $this->groups[] = new ButtonsComponent();

        $this->explainForce(Explanation::new($delegates));
    }

    /**
     * Add a tab to the body of the page.
     *
     * @param ...$delegates
     * @return $this
     */
    public function tab(...$delegates): static
    {
        if (!$this->body) {
            $this->fullBody();
        }

        array_unshift($delegates, TabContentComponent::new()->padding(3)->paddingRight(4));
        $this->body->tab($delegates);

        return $this;
    }

    /**
     * Add a body to the full width and height of the card.
     *
     * @param ...$params
     * @return CardBodyComponent
     */
    public function fullBody(...$params): CardBodyComponent
    {
        return $this->card_body()->fullSpace()->use($params);
    }

    /**
     * Add and describe a body to the card.
     *
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
     * Get the current card body.
     *
     * @return CardBodyComponent|null
     */
    public function getBody(): ?CardBodyComponent
    {
        return $this->body;
    }

    /**
     * Add and describe a search form in the card.
     *
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
     * Add and describe a model table to a card.
     *
     * @param ...$delegates
     * @return ModelTableComponent
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function model_table(...$delegates): ModelTableComponent
    {
        $body = $this->card_body()->tableResponsive()->fullSpace();
        $this->table = $body->model_table($delegates);

        $this->table->model($this->search_form);

        $this->table->createModel();

        $ad = $this->table->getActionData();

        if ($ad['show']) {
            $this->headerObj = admin_view('components.model-table.actions', $ad);
        }

        return $this->table;
    }

    /**
     * Add and describe model cards to a card.
     *
     * @param ...$delegates
     * @return ModelCardsComponent
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function model_cards(...$delegates): ModelCardsComponent
    {
        $body = $this->card_body()->tableResponsive();
        $this->table = $body->model_cards($delegates);

        $this->table->model($this->search_form);

        $this->table->createModel();

        $ad = $this->table->getActionData();

        if ($ad['show']) {
            $this->headerObj = admin_view('components.model-table.actions', $ad);
        }

        return $this->table;
    }

    /**
     * Add and describe graphs to the card.
     *
     * @param ...$delegates
     * @return ChartJsComponent
     */
    public function chart_js(...$delegates): ChartJsComponent
    {
        return $this->card_body()->chart_js(...$delegates)
            ->model($this->search_form);
    }

    /**
     * Add and describe the model info table to the card.
     *
     * @param ...$delegates
     * @return ModelInfoTableComponent
     */
    public function model_info_table(...$delegates): ModelInfoTableComponent
    {
        return $this->fullBody()->model_info_table($delegates);
    }

    /**
     * Add and describe a component for controlling order and nesting in a card.
     *
     * @param ...$delegates
     * @return NestedComponent
     */
    public function nested(...$delegates): NestedComponent
    {
        return $this->card_body()->nested(...$delegates)->model($this->search_form);
    }

    /**
     * Add and describe the form footer to the card.
     *
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
     * Add a test callback for standard instruments to the card.
     *
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
     * Add control buttons for the order control and nesting components to the card.
     *
     * @return $this
     */
    public function nestedTools(): static
    {
        $this->buttons()->nestable();

        return $this;
    }

    /**
     * Add and describe a group of buttons in the card title.
     *
     * @param  mixed  ...$delegates
     * @return ButtonsComponent
     */
    public function buttons(...$delegates): ButtonsComponent
    {
        $this->groups[] = $current = ButtonsComponent::create(...$delegates);

        return $current;
    }

    /**
     * Set the card icon which is located in the card header.
     *
     * @param  string  $name
     * @return $this
     */
    public function icon(string $name): static
    {
        $this->icon = $name;

        return $this;
    }

    /**
     * Disables card control buttons.
     *
     * @return $this
     */
    public function withoutWindowControls(): static
    {
        return $this->windowControls(false);
    }

    /**
     * Enables or disables the state of the card control buttons.
     *
     * @param  bool  $eq
     * @return $this
     */
    public function windowControls(bool $eq = true): static
    {
        $this->window_controls = $eq;

        return $this;
    }

    /**
     * Factory modal window callback.
     *
     * @param  Respond  $respond
     * @param  Request  $request
     * @return Respond
     */
    public function factoryRun(Respond $respond, Request $request): Respond
    {
        $count = (int) $request->count;
        /** @var Model $model */
        $model = (string) $request->model;

        if ($count) {
            $model::factory()->count($count)->create();

            return $respond
                ->toast_success(__('admin.factory_created', ['count' => $count]))
                ->reload();
        }

        return $respond
            ->toast_error(__('admin.factory_error'));
    }

    /**
     * Creates standard tool buttons.
     *
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Throwable
     */
    protected function makeDefaultTools(): void
    {
        if ($this->default_tools !== false) {
            /** @var Closure $test */
            $test = $this->default_tools;
            $type = $this->nowMenu?->getType() ?: 'index';
            $isRoot = admin()->isRoot();
            $factoryClassName = $this->model ? '\\Database\\Factories\\'.Str::singular(class_basename($this->model::class))
                .'Factory' : null;

            if ($test('modelInfo') && $isRoot) {

                $modal = new Modal();
                $nowMenu = admin_repo()->now;

                $infoRows = $nowMenu ? [
                    'ID' => $nowMenu->getId(),
                    __('admin.controller') => admin_repo()->currentController::class,
                    __('admin.type') => $nowMenu->getType(),
                    __('admin.route') => $nowMenu->getCurrentRoute(),
                    __('admin.link') => $nowMenu->getLink(),
                    __('admin.model') => $this->model::class,
                    __('admin.table') => $this->model->getTable(),
                    __('admin.fillable') => "<pre>" . json_encode($this->model->getFillable(), JSON_PRETTY_PRINT) . "</pre>",
                    __('admin.casts') => "<pre>" . json_encode($this->model->getCasts(), JSON_PRETTY_PRINT) . "</pre>",
                    __('admin.factory') => method_exists($this->model, 'factory') && $factoryClassName && class_exists($factoryClassName)
                        ? ModelTableComponent::callExtension('badge', [__('admin.yes'), ["success"]])
                        : ModelTableComponent::callExtension('badge', [__('admin.no'), ["danger"]]),
                    __('admin.soft-delete') => property_exists($this->model, 'forceDeleting')
                        ? ModelTableComponent::callExtension('badge', [__('admin.yes'), ["success"]])
                        : ModelTableComponent::callExtension('badge', [__('admin.no'), ["danger"]]),
                    __('admin.is-extension') => $nowMenu->getExtension()
                        ? ModelTableComponent::callExtension('badge', [__('admin.yes'), ["success"]])
                        : ModelTableComponent::callExtension('badge', [__('admin.no'), ["danger"]]),
                ] : [];

                $this->modal(
                    $modal->name('model_info_modal'),
                    $modal->title(__('admin.page-info'))->sizeBig(),
                    $modal->table()->rows($infoRows),
                    $modal->buttons()
                        ->success()
                        ->icon_times_circle()
                        ->title(__('admin.done'))
                        ->modalDestroy(),
                );

                $this->buttons()
                    ->info()
                    ->icon_info_circle()
                    ->modal('model_info_modal');
            }

            if ($test('factory') && $isRoot) {

                if (
                    $this->model
                    && method_exists($this->model, 'factory')
                    && class_exists($factoryClassName)
                ) {
                    $modal = new Modal();
                    $form = new Form();

                    $this->modal(
                        $modal->name('factory_modal'),
                        $modal->title(__('admin.factory')),
                        $modal->submitEvent([$this, 'factoryRun']),
                        $modal->form(
                            $form->input('count', 'admin.count')->default(1),
                            $form->hidden('model')->default($this->model::class),
                        ),
                        $modal->buttons()
                            ->success()
                            ->icon_paper_plane()
                            ->title(__('admin.create'))
                            ->modalSubmit(),
                    );

                    if ($type == 'index') {
                        $group = $this->buttons();
                        $group->info()
                            ->title(__('admin.factory'))
                            ->icon_industry()
                            ->modal('factory_modal');
                    }
                }
            }

            if ($test('search')) {
                if ($type !== 'create' && $type !== 'edit') {

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
                                $button->displayNone();
                            }
                        });

                    if ($this->search_form && request()->has('q')) {
                        $group->danger(['fas fa-window-close', __('admin.cancel')])
                            ->attr('id', 'cancel_search_params')
                            ->query([], ['q', 'page'])
                            ->whenRender(function (ButtonComponent $button) {
                                if (!$this->search_form || !$this->search_form->fieldsCount()) {
                                    $button->displayNone();
                                }
                            });
                    }
                }
            }

            if ($this->nowMenu && $this->nowMenu->isType('index')) {
                /** @var Model $model */
                $model = admin_repo()->modelNow;

                if ($model && property_exists($model, 'forceDeleting')) {
                    if (!request()->has('show_deleted')) {
                        $this->buttons()->dark('fas fa-trash')
                            ->on_click('location',
                                admin_url_with_get(['show_deleted' => 1]))->setTitle(__('admin.deleted'));
                    } else {
                        $this->buttons()->resourceList(admin_url_with_get([], ['show_deleted']));
                    }
                }
            }

            if ($this->nowMenu && $this->nowMenu->isResource() && !request()->has('show_deleted')) {
                $type = $this->nowMenu->getType();

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

    /**
     * Set the card title.
     *
     * @param  array|string  $title
     * @return $this
     */
    public function title(array|string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Add and describe a form to the body of the product card.
     *
     * @param ...$delegates
     * @return FormComponent
     */
    public function form(...$delegates): FormComponent
    {
        return $this->card_body()->form(...$delegates);
    }

    /**
     * Create a card footer component.
     *
     * @param  mixed  ...$params
     * @return FormFooterComponent
     */
    public function footer(...$params): FormFooterComponent
    {
        return $this->createComponent(FormFooterComponent::class, ...$params)
            ->setRow(true);
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'type' => $this->type,
            'model' => admin_repo()->modelNow,
            'title' => $this->title,
            'icon' => $this->icon,
            'footer' => fn() => $this->table?->footer(),
            'window_controls' => $this->window_controls,
            'groups' => $this->groups,
            'default_tools' => $this->default_tools,
            'search_form' => $this->search_form,
            'headerObj' => $this->headerObj
        ];
    }

    /**
     * Data for api.
     *
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function apiData()
    {
        /** @var Closure $test */
        $test = $this->default_tools;
        $type = $this->menu?->getType() ?: 'index';
        $isRoot = admin()->isRoot();
        $factoryClassName = $this->model ? '\\Database\\Factories\\'.Str::singular(class_basename($this->model::class))
            .'Factory' : null;
        $key = $this->realModel()?->getRouteKey();

        array_unshift($this->contents, $this->search_form);

        return [
            'type' => $this->type,
            'title' => $this->title,
            'icon' => $this->icon,
            'footer' => $this->table?->footerData(),
            'windowControls' => $this->window_controls,
            'groups' => $this->groups,
            'defaultTools' => $this->model && $test ? [
                'modelInfo' => $test('modelInfo') && $isRoot,
                'factory' => $test('factory') && $isRoot && method_exists($this->model, 'factory')
                    && class_exists($factoryClassName),
                'search' => $test('search') && $type !== 'create' && $type !== 'edit',
                'list' => $test('list') && AdminPermission::checkUrl($this->menu->getLinkIndex(), 'GET'),
                'edit' => $test('edit') && $type === 'show'
                    && AdminPermission::checkUrl($this->menu->getLinkEdit($key), 'PUT'),
                'info' => $test('info') && $type === 'edit'
                    && AdminPermission::checkUrl($this->menu->getLinkShow($key), 'GET'),
                'delete' => $test('delete') && $type === 'edit' || $type === 'show'
                    && AdminPermission::checkUrl($this->menu->getLinkDestroy($key), 'DELETE'),
                'add' => $test('add') && $type !== 'create'
                    && AdminPermission::checkUrl($this->menu->getLinkCreate(), 'POST'),
            ] : [],
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
        $this->makeDefaultTools();

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
}
