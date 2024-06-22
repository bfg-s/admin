<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\BladeDirectives\SystemCssBladeDirective;
use Admin\BladeDirectives\SystemJsBladeDirective;
use Admin\Components\Small\AComponent;
use Admin\Components\Small\CenterComponent;
use Admin\Components\Small\DivComponent;
use Admin\Components\Small\H1Component;
use Admin\Components\Small\H2Component;
use Admin\Components\Small\H3Component;
use Admin\Components\Small\HrComponent;
use Admin\Components\Small\IComponent;
use Admin\Components\Small\ImgComponent;
use Admin\Components\Small\PComponent;
use Admin\Components\Small\SpanComponent;
use Admin\Components\Small\TbodyComponent;
use Admin\Components\Small\TdComponent;
use Admin\Components\Small\ThComponent;
use Admin\Components\Small\TheadComponent;
use Admin\Components\Small\TrComponent;
use Admin\Controllers\SystemController;
use Admin\Core\Delegate;
use Admin\Core\MenuItem;
use Admin\Explanation;
use Admin\Page;
use Admin\Respond;
use Admin\Traits\ComponentAlpineJsTrait;
use Admin\Traits\ComponentDataEventsTrait;
use Admin\Traits\ComponentEventsTrait;
use Admin\Traits\ComponentPublicEventsTrait;
use Admin\Traits\ComponentTabsTrait;
use Admin\Traits\Delegable;
use Admin\Traits\ComponentInputControlTrait;
use Closure;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\View\View;
use Throwable;

/**
 * The main abstraction class for all components of the admin panel.
 *
 * @methods static::$components
 * @mixin ComponentMethods
 */
abstract class Component extends ComponentInputs implements Renderable, Arrayable
{
    use ComponentPublicEventsTrait;
    use ComponentInputControlTrait;
    use ComponentDataEventsTrait;
    use ComponentAlpineJsTrait;
    use ComponentEventsTrait;
    use ComponentTabsTrait;
    use Conditionable;
    use Delegable;

    /**
     * An associative array that maps component names to their corresponding class names.
     * The keys represent the component names, and the values represent the class names.
     *
     * @var array $components
     */
    public static array $components = [
        'row' => GridRowComponent::class,
        'column' => GridColumnComponent::class,
        'card' => CardComponent::class,
        'card_body' => CardBodyComponent::class,
        'search_form' => SearchFormComponent::class,
        'model_table' => ModelTableComponent::class,
        'nested' => NestedComponent::class,
        'form' => FormComponent::class,
        'model_info_table' => ModelInfoTableComponent::class,
        'buttons' => ButtonsComponent::class,
        'chart_js' => ChartJsComponent::class,
        'timeline' => TimelineComponent::class,
        'statistic_period' => StatisticPeriodComponent::class,
        'live' => LiveComponent::class,
        'watch' => WatchComponent::class,
        'field' => FieldComponent::class,
        'model_relation' => ModelRelationComponent::class,
        'modal' => ModalComponent::class,

        'lang' => LangComponent::class,
        'table' => TableComponent::class,
        'alert' => AlertComponent::class,
        'small_box' => SmallBoxComponent::class,
        'info_box' => InfoBoxComponent::class,
        'tabs' => TabsComponent::class,
        'divider' => DividerComponent::class,
        'template' => TemplateComponent::class,
        'template_area' => TemplateAreaComponent::class,
        'accordion' => AccordionComponent::class,
        'model_cards' => ModelCardsComponent::class,
        'load_content' => LoadContentComponent::class,
        'overlay' => OverlayComponent::class,

        /**
         * Small components
         */
        'div' => DivComponent::class,
        'h3' => H3Component::class,
        'h2' => H2Component::class,
        'h1' => H1Component::class,
        'hr' => HrComponent::class,
        'i' => IComponent::class,
        'thead' => TheadComponent::class,
        'tr' => TrComponent::class,
        'th' => ThComponent::class,
        'a' => AComponent::class,
        'span' => SpanComponent::class,
        'tbody' => TbodyComponent::class,
        'td' => TdComponent::class,
        'p' => PComponent::class,
        'center' => CenterComponent::class,
        'img' => ImgComponent::class,
    ];

    /**
     * The variable holds an array of scripts. Each element in the array represents a script file path or URL.
     *
     * @var array
     */
    protected static array $scripts = [];

    /**
     * The variable holds an array of styles. Each element in the array represents a style file path or URL.
     *
     * @var array $styles
     */
    protected static array $styles = [];

    /**
     * A variable to store registered input data.
     *
     * @var array|null $regInputs
     */
    protected static $regInputs = null;

    /**
     * The tag element from which the component begins.
     *
     * @var string
     */
    protected string $element = 'div';

    /**
     * Current component model.
     *
     * @var Collection|Builder|Model|Relation|null
     */
    protected Collection|Builder|Model|Relation|null $model = null;

    /**
     * The current unique name of the component model.
     *
     * @var string|null
     */
    protected string|null $model_name = null;

    /**
     * The current component model class.
     *
     * @var string|null
     */
    protected string|null $model_class = null;

    /**
     * List of applicable delegations.
     *
     * @var array
     */
    protected array $delegates = [];

    /**
     * List of delegations used by force.
     *
     * @var array
     */
    protected array $force_delegates = [];

    /**
     * Link to the instance of the current page.
     *
     * @var Page
     */
    protected Page $page;

    /**
     * The element class of the current menu item.
     *
     * @var MenuItem|null
     */
    protected MenuItem|null $menu = null;

    /**
     * A marker of whether the model is specified by the user or not.
     *
     * @var bool
     */
    protected bool $iSelectModel = false;

    /**
     * List of parent components for the content.
     *
     * @var array
     */
    protected array $contents = [];

    /**
     * A list of CSS classes that need to be applied to the first HTML element of the component.
     *
     * @var array
     */
    protected array $classes = [];

    /**
     * List of attributes that should be applied to the first HTML element of the component.
     *
     * @var array
     */
    protected array $attributes = [];

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = "default";

    /**
     * Callbacks for executing actions after the component has been rendered.
     *
     * @var Closure|array|null
     */
    protected Closure|array|null $whenRenderCallback = null;

    /**
     * The parent component of the current component.
     *
     * @var Component|null
     */
    protected ?Component $parent = null;

    /**
     * Indicates whether the component has already been initialized.
     *
     * @var bool
     */
    protected bool $isInit = false;

    /**
     * Settable date attributes.
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Realtime marker, if enabled, the component will be updated at the specified frequency.
     *
     * @var bool
     */
    protected bool $realTime = false;

    /**
     * Default delay between realtime requests.
     *
     * @var int
     */
    protected int $realTimeTimeout = 10000;

    /**
     * Counter of components.
     *
     * @var int
     */
    protected static int $counterOfComponents = 0;

    /**
     * Current component count.
     *
     * @var int
     */
    protected int $currentCount = 0;

    /**
     * Rendered modal window template.
     *
     * @var string|null
     */
    protected string|null $renderedView = null;

    /**
     * Set the component like invisible for API. return only content.
     *
     * @var bool
     */
    protected bool $invisibleForApi = false;

    /**
     * Is finally for API contents. without contents.
     *
     * @var bool
     */
    protected bool $finallyForApi = false;

    /**
     * Check if the component is ignore for API contents. without component.
     *
     * @var bool
     */
    public bool $ignoreForApi = false;

    /**
     * A model relation that is used in the component.
     *
     * @var array
     */
    protected array $relations = [];

    /**
     * Component constructor.
     *
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        $this->page = app(Page::class);

        $this->menu = $this->page->menu;

        $this->model($this->page->model());

        $this->iSelectModel = false;

        $this->delegates(...$delegates);

        $this->currentCount = ++static::$counterOfComponents;

        $this->checkHasHeadersTrapForComponentApi();
    }

    /**
     * Assign a model, builder, or relation to a component.
     *
     * @param $model
     * @return $this
     */
    public function model($model = null): static
    {
        if ($model || !$this->model) {
            if (is_callable($model)) {
                $model = call_user_func($model, $this->model ?: $this->page->model());
            }

            $m = $this->model ?: $this->page->model();
            if (is_array($model) && !isset($model[0]) && $m) {
                $model = eloquent_instruction($m, $model);
            }

            if (is_string($model) && class_exists($model)) {
                $model = new $model();
            }

            $search = false;
            if ($model instanceof SearchFormComponent) {
                $model = $model->makeModel($this->model ?: $this->page->model());
                $search = true;
            }

            $this->model = $model ?? $this->page->model();
            if (!$this->model_name) {
                $this->model_name = $this->page->getModelName($model);
            }
            if (!$search) {
                $c = $this->realModel();
                if ($c && !is_array($c)) {
                    $class = is_string($c) ? $this->model : get_class($c);
                    $this->menu = $this->page->findModelMenu($class);
                }
            }
            $this->iSelectModel = true;
        }

        return $this;
    }

    /**
     * Reset counter of components
     *
     * @return void
     */
    public static function reset(): void
    {
        static::$counterOfComponents = 0;
    }

    /**
     * Load model relations.
     *
     * @param ...$relations
     * @return $this
     */
    public function modelLoad(...$relations): static
    {
        foreach ($relations as $relation) {
            if (is_array($relation) && ! is_assoc($relation)) {
                $this->modelLoad(...$relation);
            } else if (
                $this->model instanceof Model
            ) {
                if ($this->model->exists) {
                    if (! $this->model->relationLoaded(is_array($relation) ? $relation[0] : $relation)) {
                        $this->model->load($relation);
                    }
                } else {
                    $this->relations[] = $relation;
                }
            }
        }

        return $this;
    }

    /**
     * Get the real model from the current model (since the component may have a builder or relation)
     *
     * @return mixed
     */
    public function realModel(): mixed
    {
        if (
            $this->model instanceof Builder
            || $this->model instanceof Relation
        ) {
            return $this->model->getModel();
        }

        return $this->model;
    }

    /**
     * Add delegations to the component.
     * The delegation is a class that implements the Delegate interface.
     *
     * @param ...$delegates
     * @return $this
     */
    public function delegates(...$delegates): static
    {
        $this->delegates = [
            ...$this->delegates,
            ...$delegates,
        ];

        return $this;
    }

    /**
     * Get all scripts links that are connected to the component.
     *
     * @return array
     */
    public static function getScripts(): array
    {
        return self::$scripts;
    }

    /**
     * Get all style links that are connected to the component.
     *
     * @return array
     */
    public static function getStyles(): array
    {
        return self::$styles;
    }

    /**
     * Static component helper for creating a new component instance.
     *
     * @param  array  $data
     * @return static
     */
    public static function create(...$data): static
    {
        return new static(...$data);
    }

    /**
     * Registration of a new input form.
     *
     * @param  string  $name
     * @param  string  $class
     * @return void
     */
    public static function registerFormComponent(string $name, string $class): void
    {
        static::$inputs[$name] = $class;
    }

    /**
     * Merge the list of new form inputs.
     *
     * @param  array  $array
     * @return void
     */
    public static function mergeFormComponents(array $array): void
    {
        static::$inputs = array_merge(static::$inputs, $array);
    }

    /**
     * Register a new admin panel component.
     *
     * @param  string  $name
     * @param  string  $class
     * @return void
     */
    public static function registerComponent(string $name, string $class): void
    {
        static::$components[$name] = $class;
    }

    /**
     * Set the component ID attribute.
     *
     * @param  string  $id
     * @return $this
     */
    public function setId(string $id): static
    {
        $this->attr('id', $id);

        return $this;
    }

    /**
     * Set the component attribute.
     *
     * @param  string|array  $name
     * @param  mixed  $value
     * @return $this
     */
    public function attr(string|array $name, mixed $value = null): static
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->attr($k, $v);
            }
        } else {
            if (is_array($value)) {
                $value = json_encode($value);
                $name = ":".$name;
            }
            $added = false;
            if ($value instanceof Respond) {
                if (isset($this->attributes[$name])) {
                    $this->attributes[$name] = $this->attributes[$name]->merge($value);
                    $added = true;
                }
            }

            if (!$added) {
                $this->attributes[$name] = $value;
            }
        }

        return $this;
    }

    /**
     * Add text to content components.
     *
     * @param $text
     * @return $this
     */
    public function text($text): static
    {
        $this->contents[] = $text;

        return $this;
    }

    /**
     * Set the values of the attribute "data-*".
     *
     * @param  array  $datas
     * @return Component
     */
    public function setDatas(array $datas): static
    {
        foreach ($datas as $key => $data) {
            $this->attr("data-{$key}", is_array($data) ? implode(' && ', $data) : $data);
        }

        return $this;
    }

    /**
     * Set the values of the attribute "data-rule-*".
     *
     * @param  array  $rules
     * @return Component
     */
    public function setRules(array $rules): static
    {
        foreach ($rules as $key => $data) {
            $this->attr("data-rule-{$key}", is_array($data) ? implode(' && ', $data) : $data);
        }

        return $this;
    }

    /**
     * Use a closure for a component.
     *
     * @param  callable|array|string  $callable
     * @return $this
     */
    public function use(callable|array|string $callable): static
    {
        if (is_array($callable)) {
            foreach ($callable as $item) {
                $this->use($item);
            }
        } else {
            if (is_string($callable)) {
                $this->appEnd($callable);
            } else {
                if (is_callable($callable)) {
                    call_user_func($callable, $this);
                }
            }
        }

        return $this;
    }

    /**
     * Add content to the end of the content list of components.
     *
     * @param  mixed  $content
     * @return $this
     */
    public function appEnd(mixed $content = ""): static
    {
        $this->contents[] = $content;

        return $this;
    }

    /**
     * Dump components.
     *
     * @return $this
     */
    public function dump(): static
    {
        dump($this);

        return $this;
    }

    /**
     * Add a callback for when the component is rendering.
     *
     * @param  array|Closure  $call
     * @return $this
     */
    public function whenRender(array|Closure $call): static
    {
        if (is_embedded_call($call)) {
            $this->whenRenderCallback = $call;
        }

        return $this;
    }

    /**
     * A magic method that turns a component into a string.
     *
     * @return string
     * @throws Throwable
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * Disable component real-time updates.
     *
     * @return $this
     */
    public function withoutRealtime(): static
    {
        $this->realTime = false;

        return $this;
    }

    /**
     * Check if the component is real-time.
     *
     * @return bool
     */
    public function isRealtime(): bool
    {
        return $this->realTime;
    }

    /**
     * Timeout time in milliseconds for real-time updates.
     *
     * @param  int  $timeout
     * @return $this
     */
    public function setTimeout(int $timeout): static
    {
        $this->realTimeTimeout = $timeout;

        return $this;
    }

    /**
     * Render the components.
     *
     * @return string
     * @throws Throwable
     */
    public function render(): string
    {
        $this->initComponent();

        $renderedView = admin_view('components.'.$this->view, array_merge([
            'contents' => $this->contents,
            'element' => $this->element,
            'attributes' => $this->attributes,
        ], $this->viewData()))->render();

        $this->renderedView = $this->afterRenderEvent($renderedView);

        return $this->renderedView;
    }

    /**
     * Get a rendered modal window template.
     *
     * @return string|null
     */
    public function getRenderedView(): string|null
    {
        return $this->renderedView;
    }

    /**
     * A function that runs before the main rendering applies delegations and a model.
     *
     * @return void
     */
    protected function onRender(): void
    {
        $this->newExplain($this->delegates);
        $this->newExplainForce($this->force_delegates);
        if (!$this->iSelectModel && ($this->parent?->model ?? null)) {
            $this->model($this->parent->model);
        }
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    abstract protected function mount(): void;

    /**
     * Get the JavaScript that belongs to the component to be displayed on the page.
     *
     * @return string
     */
    public function js(): string
    {
        return <<<JS

JS;
    }

    /**
     * Get the CSS that belongs to the component to be displayed on the page.
     *
     * @return string
     */
    public function css(): string
    {
        return <<<CSS

CSS;
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return array
     */
    protected function viewData(): array
    {
        return [];
    }

    /**
     * Trap for the component view data.
     *
     * @return array
     */
    public function getViewDate(): array
    {
        return $this->viewData();
    }

    /**
     * Method to override, used to add events to the component after rendering.
     *
     * @param $renderedView
     * @return string
     */
    protected function afterRenderEvent($renderedView): string
    {
        return $renderedView;
    }

    /**
     * Add any content type to the end of the content list of the components sheet.
     *
     * @param  mixed  $data
     * @return $this
     */
    public function add(mixed $data): static
    {
        return $this->appEnd($data);
    }

    /**
     * Add content to the beginning of the content list of components.
     *
     * @param  mixed  $content
     * @return $this
     */
    public function prepEnd(mixed $content = ""): static
    {
        array_unshift($this->contents, $content);

        return $this;
    }

    /**
     * Add the admin panel blade theme template to the end of the list of components sheet content.
     *
     * @param $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return $this
     */
    public function view($view = null, array $data = [], array $mergeData = []): static
    {
        return $this->appEnd(
            str_contains($view, '::') ? view($view, $data, $mergeData) : admin_view($view, $data, $mergeData)
        );
    }

    /**
     * Add the blade template to the end of the content list of the components sheet.
     *
     * @param $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return $this
     */
    public function originView($view = null, array $data = [], array $mergeData = []): static
    {
        return $this->appEnd(
            view($view, $data, $mergeData)
        );
    }

    /**
     * Create a new VUE component.
     *
     * @param  string  $class
     * @param  array  $params
     * @return Component
     */
    public function vue(string $class, array $params = []): static
    {
        $this->appEnd(
            $this->createComponent($class)?->attr($params)
        );

        return $this;
    }

    /**
     * Create a new component.
     *
     * @template CLASS
     * @param  string|CLASS  $componentClass
     * @param ...$arguments
     * @return Component|CLASS
     */
    public function createComponent(string $componentClass, ...$arguments)
    {
        /** @var Component $newObj */
        $newObj = new $componentClass(...$arguments);

        $newObj->setParent($this);

        return $newObj;
    }

    /**
     * Set a reference to this object of a particular variable also by reference.
     *
     * @param $link
     * @return $this
     */
    public function haveLink(&$link): static
    {
        $link = $this;

        return $this;
    }

    /**
     * Roughly assign a model to a component.
     *
     * @param $model
     * @return $this
     */
    public function forceSetModel($model): static
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Add delegations for enforcement.
     *
     * @param ...$delegates
     * @return $this
     */
    public function forceDelegates(...$delegates): static
    {
        $this->force_delegates = [
            ...$this->force_delegates,
            ...$delegates,
        ];

        return $this;
    }

    /**
     * Get the class of the real model.
     *
     * @return string|null
     */
    public function realModelClass(): ?string
    {
        $model = $this->realModel();

        return $model ? get_class($model) : null;
    }

    /**
     * Apply a data collection to a component.
     *
     * @param $collection
     * @param  callable  $callback
     * @return $this
     */
    public function withCollection($collection, callable $callback): static
    {
        foreach ($collection as $key => $item) {
            $result = call_user_func($callback, $item, $key);

            if ($result && is_array($result)) {
                $this->forceDelegateNow(
                    ...$result
                );
            } else if ($result instanceof Delegate) {
                $this->forceDelegateNow($result);
            }
        }

        return $this;
    }

    /**
     * Apply delegations immediately and forcefully.
     *
     * @param ...$delegates
     * @return $this
     */
    public function forceDelegateNow(...$delegates): static
    {
        $this->newExplainForce($delegates);

        return $this;
    }

    /**
     * Apply a callback that can return delegations and explanations.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function with(callable $callback): static
    {
        $data = call_user_func($callback, $this);

        if ($data && is_array($data)) {
            $this->delegatesNow($data);
        } elseif ($data && $data instanceof Delegate) {
            $this->delegatesNow([$data]);
        } elseif ($data && $data instanceof Explanation) {
            $this->explainForce($data);
        }

        return $this;
    }

    /**
     * Apply delegations immediately.
     *
     * @param ...$delegates
     * @return $this
     */
    public function delegatesNow(...$delegates): static
    {
        $this->newExplain($delegates);

        return $this;
    }

    /**
     * A magic method to handle adding components to the content list of components.
     *
     * @param $name
     * @param $arguments
     * @return bool|FormComponent|mixed|string
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (preg_match('/^_(.*)/', $name, $m)) {
            $name = $m[1];
            return $this->parent?->{$name}(...$arguments);
        }

        if (!Component::$regInputs) {
            $inputs = Component::$regInputs = implode('|', array_keys(Component::$inputs));
        } else {
            $inputs = Component::$regInputs;
        }

        if (
            preg_match("/^($inputs)_(.+)$/", $name, $matches)
            && !isset(Component::$inputs[$name])
            && !Component::hasComponentStatic($name)
        ) {
            $field = $matches[1];
            $name = str_replace(['_dot_', '__'], '.', Str::snake($matches[2], '_'));
            $label = $arguments[0] ?? ucfirst(str_replace(['.', '_'], ' ', $name));

            return $this->{$field}($name, Lang::has("admin.$label") ? __("admin.$label") : $label);
        } else {
            if ($this->hasComponent($name)) {
                if ($object = $this->getComponent($name)) {
                    $this->appEnd(
                        $newObj = $this->createComponent($object, ...$arguments)
                    );
                    return $newObj;
                }
            } else {

                if ($call = $this->initInput($name, $arguments)) {
                    return $call;
                }
            }
        }

        throw new Exception("Method [$name] not found! In [".static::class."]");
    }

    /**
     * Magic static call.
     *
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return static::create()->$name(...$arguments);
    }

    /**
     * Method for initializing an input in the content of a component.
     *
     * @param $name
     * @param  array  $arguments
     * @return bool|InputGroupComponent|mixed
     */
    protected function initInput($name, array $arguments): mixed
    {
        if (isset(static::$inputs[$name])) {
            $class = static::$inputs[$name];

            $class = new $class(...$arguments);

            if ($class instanceof InputGroupComponent) {
                $class->set_parent($this);

                $class->model($this->model);

                if ($this->vertical) {
                    $class->vertical();
                }

                if ($this->reversed) {
                    $class->reversed();
                }

                if ($this->labelWidth !== null) {
                    $class->label_width($this->labelWidth);
                }
            }

            if ($this->set) {
                $this->appEnd($class);
            } else {
                $class->unregister();
            }

            $this->set = true;

            return $class;
        }

        return false;
    }

    /**
     * Static method to check if a component exists.
     *
     * @param  string  $name
     * @return bool
     */
    public static function hasComponentStatic(string $name): bool
    {
        return isset(static::$components[$name]);
    }

    /**
     * Check if the input exists.
     *
     * @param  string  $name
     * @return bool
     */
    public static function hasInput(string $name): bool
    {
        return isset(static::$inputs[$name]);
    }

    /**
     * Check if the component exists.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasComponent(string $name): bool
    {
        return isset(static::$components[$name]);
    }

    /**
     * Get component class by name.
     *
     * @param  string  $name
     * @return string|null
     */
    public function getComponent(string $name): ?string
    {
        return static::$components[$name] ?? null;
    }

    /**
     * A magic method for applying properties like inserting components into a content list.
     *
     * @param  string  $name
     * @return $this
     */
    public function __get(string $name)
    {
        if (!Component::$regInputs) {
            $inputs = Component::$regInputs = implode('|', array_keys(Component::$inputs));
        } else {
            $inputs = Component::$regInputs;
        }

        if (
            preg_match("/^($inputs)_(.+)$/", $name, $matches)
            && !isset(Component::$inputs[$name])
            && !Component::hasComponentStatic($name)
        ) {
            $field = $matches[1];
            $name = str_replace(['_dot_', '__'], '.', Str::snake($matches[2], '_'));
            $label = ucfirst(str_replace(['.', '_'], ' ', $name));

            return $this->{$field}($name, Lang::has("admin.$name") ? __("admin.$name") : $label);
        } else {
            if (method_exists($this, $name)) {
                return $this->{$name}();
            }
        }

        return $this;
    }

    /**
     * Set the title attribute, which is responsible for displaying text on hover.
     *
     * @param  string  $title
     * @return $this
     */
    public function setTitle(string $title): static
    {
        $this->attr('title', $title);

        return $this;
    }

    /**
     * Set the title attribute, which is responsible for displaying text on hover. If the conditions are met.
     *
     * @param $condition
     * @param  string  $title
     * @return $this
     */
    public function setTitleIf($condition, string $title): static
    {
        if ($condition) {
            $this->attr('title', $title);
        }

        return $this;
    }

    /**
     * Check if the component attribute exists.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Get a component attribute by name.
     *
     * @param  string  $name
     * @return mixed
     */
    public function getAttribute(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Set component attributes by name.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Hide a component on the page, will add a display none.
     *
     * @param  bool  $eq
     * @return $this
     */
    public function hide(mixed $eq = true): static
    {
        if ($eq) {
            $this->attr('style', 'display: none');
        }

        return $this;
    }

    /**
     * Get the name generated by nested components.
     *
     * @return array
     */
    public function deepNames(): array
    {
        $names = [];
        $parent = $this;

        do {
            if (method_exists($parent, 'deepName')) {
                $parentNames = array_reverse(
                    array_filter(
                        $this->parseStringToArray(
                            $parent->deepName($names) ?: ''
                        )
                    )
                );
                $names = array_merge($names, $parentNames);
            }
            $parent = $parent->getParent();
        } while ($parent);

        return collect($names)
            ->reverse()
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Helper function for parsing a name string into an array.
     *
     * @param  string  $str
     * @return array
     */
    protected function parseStringToArray(string $str): array
    {
        $arr = explode('[', $str);
        return array_map(function ($s) {
            return trim(trim(trim($s), '[]'));
        }, $arr);
    }

    /**
     * The deep name function is present in the tree of all components and generates the name nested.
     *
     * @param  array  $names
     * @return string|null
     */
    public function deepName(array $names): string|null
    {
        return null;
    }

    /**
     * Get the parent component.
     *
     * @return \Admin\Components\Component|null
     */
    public function getParent(): Component|null
    {
        return $this->parent;
    }

    /**
     * Set the parent component.
     *
     * @param  Component  $component
     * @return $this
     */
    public function setParent(Component $component): static
    {
        $this->parent = $component;

        return $this;
    }

    /**
     * Get the nested path of all parents.
     *
     * @return array
     */
    public function deepPaths(): array
    {
        $paths = [];
        $parent = $this;
        do {
            if (method_exists($parent, 'deepName')) {
                $parentPaths = array_reverse(
                    array_filter(
                        explode('.', $parent->deepPath($paths) ?: '')
                    )
                );
                $paths = array_merge($paths, $parentPaths);
            }
            $parent = $parent->getParent();
        } while ($parent);

        return collect($paths)
            ->reverse()
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Generate part of the path for a nested pass.
     *
     * @param  array  $paths
     * @return string|null
     */
    public function deepPath(array $paths): string|null
    {
        return null;
    }

    /**
     * Merge a list of custom date attributes with the current component.
     *
     * @param  array  $datas
     * @return $this
     */
    public function mergeDataList(array $datas): static
    {
        $this->data = array_merge(
            $this->data,
            $datas
        );

        return $this;
    }

    /**
     * A component method for initializing a component.
     *
     * @return void
     */
    protected function initComponent(): void
    {
        if (!$this->isInit) {
            $this->onRender();

            $this->mount();

            SystemJsBladeDirective::addComponentJs($this->js());
            SystemCssBladeDirective::addComponentCss($this->css());

            $this->isInit = true;

            if ($this->realTime) {

                $this->dataLoad('realtime', [
                    'name' => 'component-' . $this->currentCount,
                    'timeout' => $this->realTimeTimeout,
                ]);

                SystemController::$realtimeComponents['component-' . $this->currentCount] = $this;
            }
        }

        if (is_embedded_call($this->whenRenderCallback)) {

            call_user_func($this->whenRenderCallback, $this);
        }
    }

    /**
     * Check if the component is invisible for the API.
     * If the header is set, the component will be invisible.
     * @return void
     */
    protected function checkHasHeadersTrapForComponentApi(): void
    {
        $name = str_replace("\\", "-", static::class);
        $request = request();

        if ($request->hasHeader($name)) {

            $this->invisibleForApi
                = ! ((int) $request->header($name));
        }

        if ($request->hasHeader($name . '-Ignore')) {

            $this->ignoreForApi
                = !! ((int) $request->header($name . '-Ignore'));
        }
    }

    /**
     * The original model class of the component.
     *
     * @return string|null
     */
    public function getOriginalModelClass(): string|null
    {
        if ($this->model instanceof Model) {
            return get_class($this->model);
        } else if ($this->model instanceof Builder) {
            return get_class($this->model->getModel());
        } else if ($this->model instanceof Relation) {
            return get_class($this->model->getRelated());
        } else if ($this->model instanceof Collection) {
            $first = $this->model->first();
            return is_object($first) ? get_class($first) : null;
        }
        return null;
    }

    /**
     * Method for export component data to the API.
     *
     * @return array
     */
    public function exportToApi(): array
    {
        $this->initComponent();

        if ($this->invisibleForApi) {

            return $this->page->upgradeDataToApiResponse($this->contents);
        }

        $addApiData = method_exists($this, 'apiData') ? $this->apiData() : $this->viewData();

        foreach ($addApiData as $key => $value) {

            if (is_array($value) && ! is_assoc($value)) {

                $addApiData[$key] = $this->page->upgradeDataToApiResponse($value);
            } else if ($value instanceof Component) {
                if (! $value->ignoreForApi) {
                    $addApiData[$key] = collect($value->exportToApi())->first();
                }
            } else if ($value instanceof Model) {
                $addApiData[$key] = $value->exists ? $value->toArray() : $value::class;
            }
        }

        return [array_merge([
            'component' => static::class,
            //'model' => $this->getOriginalModelClass(),
            'modelName' => $this->model_name,
            'attributes' => $this->attributes,
        ], $addApiData, ! $this->finallyForApi ? [
            'contents' => $this->page->upgradeDataToApiResponse($this->contents)
        ] : [])];
    }

    /**
     * Get component like an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->exportToApi();
    }
}
