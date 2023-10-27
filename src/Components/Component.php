<?php

namespace Admin\Components;

use Admin\BladeDirectives\SystemCssBladeDirective;
use Admin\BladeDirectives\SystemJsBladeDirective;
use Admin\Components\Inputs\AmountInput;
use Admin\Components\Inputs\AutocompleteInput;
use Admin\Components\Inputs\ChecksInput;
use Admin\Components\Inputs\CKEditorInput;
use Admin\Components\Inputs\CodeMirrorInput;
use Admin\Components\Inputs\ColorInput;
use Admin\Components\Inputs\DateInput;
use Admin\Components\Inputs\DateRangeInput;
use Admin\Components\Inputs\DateTimeInput;
use Admin\Components\Inputs\DateTimeRangeInput;
use Admin\Components\Inputs\DualSelectInput;
use Admin\Components\Inputs\EmailInput;
use Admin\Components\Inputs\FileInput;
use Admin\Components\Inputs\HiddenInput;
use Admin\Components\Inputs\IconInput;
use Admin\Components\Inputs\ImageInput;
use Admin\Components\Inputs\InfoCreatedAtInput;
use Admin\Components\Inputs\InfoIdInput;
use Admin\Components\Inputs\InfoInput;
use Admin\Components\Inputs\InfoUpdatedAtInput;
use Admin\Components\Inputs\Input;
use Admin\Components\Inputs\MDEditorInput;
use Admin\Components\Inputs\MultiSelectInput;
use Admin\Components\Inputs\NumberInput;
use Admin\Components\Inputs\NumericInput;
use Admin\Components\Inputs\PasswordInput;
use Admin\Components\Inputs\RadiosInput;
use Admin\Components\Inputs\RatingInput;
use Admin\Components\Inputs\SelectInput;
use Admin\Components\Inputs\SelectTagsInput;
use Admin\Components\Inputs\SliderInput;
use Admin\Components\Inputs\SwitcherInput;
use Admin\Components\Inputs\TextareaInput;
use Admin\Components\Inputs\TimeInput;
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
use Admin\Core\Delegate;
use Admin\Core\MenuItem;
use Admin\Explanation;
use Admin\Jax\Admin;
use Admin\Page;
use Admin\Traits\AlpineInjectionTrait;
use Admin\Traits\BootstrapClassHelpers;
use Admin\Traits\BuildHelperTrait;
use Admin\Traits\DataAttributes;
use Admin\Traits\DataTrait;
use Admin\Traits\Delegable;
use Admin\Traits\FieldMassControlTrait;
use Closure;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\View\View;
use Throwable;

/**
 * @methods static::$inputs
 * @mixin Components
 * @mixin ComponentMethods
 */
abstract class Component implements Renderable
{
    use FieldMassControlTrait;
    use BuildHelperTrait;
    use Delegable;
    use DataTrait;
    use DataAttributes;
    use BootstrapClassHelpers;
    use AlpineInjectionTrait;
    use Conditionable;

    /**
     * @var string
     */
    protected $element = 'div';

    /**
     * @var array
     */
    public static array $inputs = [
        'input' => Input::class,
        'password' => PasswordInput::class,
        'email' => EmailInput::class,
        'number' => NumberInput::class,
        'numeric' => NumericInput::class,
        'amount' => AmountInput::class,
        'file' => FileInput::class,
        'image' => ImageInput::class,
        'switcher' => SwitcherInput::class,
        'date_range' => DateRangeInput::class,
        'date_time_range' => DateTimeRangeInput::class,
        'date' => DateInput::class,
        'date_time' => DateTimeInput::class,
        'time' => TimeInput::class,
        'icon' => IconInput::class,
        'color' => ColorInput::class,
        'select' => SelectInput::class,
        'dual_select' => DualSelectInput::class,
        'multi_select' => MultiSelectInput::class,
        'select_tags' => SelectTagsInput::class,
        'textarea' => TextareaInput::class,
        'ckeditor' => CKEditorInput::class,
        'mdeditor' => MDEditorInput::class,
        'checks' => ChecksInput::class,
        'radios' => RadiosInput::class,
        'codemirror' => CodeMirrorInput::class,
        'info' => InfoInput::class,
        'info_id' => InfoIdInput::class,
        'info_created_at' => InfoCreatedAtInput::class,
        'info_updated_at' => InfoUpdatedAtInput::class,
        'rating' => RatingInput::class,
        'hidden' => HiddenInput::class,
        'autocomplete' => AutocompleteInput::class,
        'slider' => SliderInput::class,
    ];

    /**
     * @var array|string[]
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
     * @var array
     */
    protected static array $scripts = [];

    /**
     * @var array
     */
    protected static array $styles = [];

    /**
     * @var string|null
     */
    protected static $regInputs = null;

    /**
     * @var string
     */
    protected $class = null;

    /**
     * @var Builder|Model|Relation|null
     */
    protected $model = null;

    /**
     * @var string|null
     */
    protected $model_name = null;

    /**
     * @var string|null
     */
    protected $model_class = null;

    /**
     * @var array
     */
    protected array $delegates = [];

    /**
     * @var array
     */
    protected array $force_delegates = [];

    /**
     * @var Page
     */
    protected Page $page;

    /**
     * @var MenuItem|null
     */
    protected ?MenuItem $menu = null;

    /**
     * @var bool
     */
    protected bool $iSelectModel = false;

    /**
     * @var array
     */
    protected array $contents = [];

    /**
     * @var array
     */
    protected array $classes = [];

    /**
     * @var array
     */
    protected array $attributes = [];

    /**
     * @var Closure[]|array[]
     */
    protected array $rendered = [];

    /**
     * @var string
     */
    protected string $view = "default";

    /**
     * When render closure.
     *
     * @var Closure|array|null
     */
    protected Closure|array|null $wrc = null;

    /**
     * @var Component|null
     */
    protected ?Component $parent = null;

    /**
     * @var bool
     */
    protected bool $isInit = false;

    /**
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        $this->page = app(Page::class);

        $this->model();

        $this->iSelectModel = false;

        $this->delegates(...$delegates);

        if ($this->class) {
            $this->addClass($this->class);
        }
    }

    /**
     * @return array
     */
    public static function getScripts(): array
    {
        return self::$scripts;
    }

    /**
     * @return array
     */
    public static function getStyles(): array
    {
        return self::$styles;
    }

    /**
     * @return View|string
     */
    public function render(): View|string
    {
        if (! $this->isInit) {

            $this->onRender();

            $this->mount();

            SystemJsBladeDirective::addComponentJs($this->js());
            SystemCssBladeDirective::addComponentCss($this->css());

            $this->isInit = true;
        }


        if (is_embedded_call($this->wrc)) {
            call_user_func($this->wrc, $this);
        }

        foreach ($this->rendered as $item) {
            call_user_func($item, $this);
        }

        return admin_view('components.' . $this->view, array_merge([
            'contents' => $this->contents,
            'classes' => $this->classes,
            'element' => $this->element,
            'attributes' => $this->attributes,
        ], $this->viewData()));
    }

    /**
     * @param  string  $id
     * @return $this
     */
    public function setId(string $id): static
    {
        $this->attr('id', $id);

        return $this;
    }

    /**
     * @param $text
     * @return $this
     */
    public function text($text): static
    {
        $this->contents[] = $text;

        return $this;
    }

    /**
     * @return array
     */
    protected function viewData(): array
    {
        return [];
    }

    /**
     * Set the values of the attribute "data-*".
     *
     * @param array $datas
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
     * @param  callable|array|string  $callable
     * @return $this
     */
    public function use(callable|array|string $callable): static
    {
        if (is_array($callable)) {
            foreach ($callable as $item) {
                $this->use($item);
            }
        } else if (is_string($callable)) {
            $this->appEnd($callable);
        } else if (is_callable($callable)) {
            call_user_func($callable, $this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function dump(): static
    {
        dump($this);

        return $this;
    }

    /**
     * @param  string|array  $name
     * @param mixed $value
     * @return $this
     */
    public function attr(string|array $name, mixed $value = null): static
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {

                $this->attr($k, $v);
            }
        } else {

            if ($name == 'class') {

                $this->classes[] = $value;

            } else {

                $this->attributes[$name] = $value;
            }
        }

        return $this;
    }

    /**
     * Set when render closure.
     *
     * @param  array|Closure  $call
     * @return $this
     */
    public function whenRender(array|Closure $call): static
    {
        if (is_embedded_call($call)) {
            $this->wrc = $call;
        }

        return $this;
    }

    /**
     * @return string
     * @throws Throwable
     */
    public function __toString(): string
    {
        $render = $this->render();

        if ($render instanceof Renderable) {

            $render = $render->render();
        }

        return $render;
    }

    /**
     * Add new child.
     *
     * @param  mixed  $data
     * @return $this
     */
    public function add(mixed $data): static
    {
        return $this->appEnd($data);
    }

    /**
     * @param  mixed  ...$classes
     * @return $this
     */
    public function addClass(...$classes): static
    {
        foreach ($classes as $class) {
            if (is_array($class)) {
                $this->classes = array_merge($this->classes, $class);
            } else {
                $this->classes[] = $class;
            }
        }

        return $this;
    }

    /**
     * @param $condition
     * @param ...$classes
     * @return $this
     */
    public function addClassIf($condition, ...$classes): static
    {
        if ($condition) {

            return $this->addClass(...$classes);
        }

        return $this;
    }

    /**
     * @param mixed $content
     * @return $this
     */
    public function prepEnd(mixed $content = ""): static
    {
        array_unshift($this->contents, $content);

        return $this;
    }

    /**
     * @param mixed $content
     * @return $this
     */
    public function appEnd(mixed $content = ""): static
    {
        $this->contents[] = $content;

        return $this;
    }

    /**
     * @param $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return $this
     */
    public function view($view = null, array $data = [], array $mergeData = []): static
    {
        return $this->appEnd(
            admin_view($view, $data, $mergeData)
        );
    }

    /**
     * @param  string  $class
     * @param  array  $params
     * @return Component
     */
    public function vue(string $class, array $params = []): static
    {
        $this->createComponent($class)?->attr($params);

        return $this;
    }

    /**
     * Static create.
     *
     * @param array $data
     * @return static
     */
    public static function create(...$data): static
    {
        return new static(...$data);
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
            if (is_array($model) && ! isset($model[0]) && $m) {
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
            if (! $this->model_name) {
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
     * @return Builder|Model|Relation|null
     */
    public function realModel(): Model|Relation|Builder|null
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
     * @param $name
     * @param $arguments
     * @return Component|FormGroupComponent|bool|mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if ($call = static::static_call_group($name, $arguments)) {
            return $call;
        }

        return (new static())->{$name}(...$arguments);
    }

    /**
     * @param  string  $name
     * @param  string  $class
     * @return void
     */
    public static function registerFormComponent(string $name, string $class): void
    {
        static::$inputs[$name] = $class;
    }

    /**
     * @param  array  $array
     * @return void
     */
    public static function mergeFormComponents(array $array): void
    {
        static::$inputs = array_merge(static::$inputs, $array);
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public static function has(string $name): bool
    {
        return isset(static::$inputs[$name]);
    }

    /**
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
     * @param ...$delegates
     * @return $this
     */
    public function forceDelegateNow(...$delegates): static
    {
        $this->newExplainForce($delegates);

        return $this;
    }

    /**
     * @return string|null
     */
    public function realModelClass(): ?string
    {
        $model = $this->realModel();

        return $model ? get_class($model) : null;
    }

    /**
     * @param $collection
     * @param  callable  $callback
     * @return $this
     */
    public function withCollection($collection, callable $callback): static
    {
        foreach ($collection as $key => $item) {
            $this->with(fn() => call_user_func($callback, $item, $key));
        }

        return $this;
    }

    /**
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
     * @param ...$delegates
     * @return $this
     */
    public function delegatesNow(...$delegates): static
    {
        $this->newExplain($delegates);

        return $this;
    }

    /**
     * Event after render.
     * @param  array|Closure  $call
     * @return $this
     */
    public function rendered(array|Closure $call): static
    {
        if (is_embedded_call($call)) {
            $this->rendered[] = $call;
        }

        return $this;
    }

    /**
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
        } else if ($this->hasComponent($name)) {
            if ($object = $this->getComponent($name)) {

                $this->appEnd(
                    $newObj = $this->createComponent($object, ...$arguments)
                );
                return $newObj;
            }
        } else {
            if ($call = $this->call_group($name, $arguments)) {
                return $call;
            }
        }

        throw new Exception("Method [$name] not found! In [" . static::class . "]");
    }

    /**
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

        $newObj->model($this->realModel());

        return $newObj;
    }

    /**
     * @param  Component  $component
     * @return $this
     */
    public function setParent(Component $component): static
    {
        $this->parent = $component;

        return $this;
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function hasComponent(string $name): bool
    {
        return isset(static::$components[$name]);
    }

    /**
     * @param  string  $name
     * @return string|null
     */
    public function getComponent(string $name): ?string
    {
        return static::$components[$name] ?? null;
    }

    /**
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
     * @return void
     */
    public function onRender(): void
    {
        $this->newExplain($this->delegates);
        $this->newExplainForce($this->force_delegates);
        if (!$this->iSelectModel && ($this->parent?->model ?? null)) {
            $this->model($this->parent->model);
        }
    }

    /**
     * Component mount method.
     * @return void
     */
    abstract protected function mount(): void;

    /**
     * @param  callable  $callback
     * @param  array  $parameters
     * @return $this
     */
    public function click(callable $callback, array $parameters = []): static
    {
        $this->on_click(
            static::registerCallBack($callback, $parameters, $this->model)
        );

        return $this;
    }

    /**
     * @param  callable  $callback
     * @param  array  $parameters
     * @param $model
     * @return array[]
     */
    public static function registerCallBack(callable $callback, array $parameters = [], $model = null): array
    {
        Admin::$callbacks[] = $callback;

        if ($model) {
            foreach ($parameters as $key => $parameter) {
                if (is_int($key) && is_string($parameter)) {
                    $parameters[$parameter] = multi_dot_call($model, $parameter);
                    unset($parameters[$key]);
                } else {
                    if (is_callable($parameter)) {
                        $parameters[$key] = call_user_func($parameter, $model);
                    }
                }
            }
        }

        return [
            'jax.admin.call_callback' => [
                array_key_last(Admin::$callbacks),
                $parameters
            ]
        ];
    }

    /**
     * @param  callable  $callback
     * @param  array  $parameters
     * @return $this
     */
    public function dblclick(callable $callback, array $parameters = []): static
    {
        $this->on_dblclick(
            static::registerCallBack($callback, $parameters, $this->model)
        );

        return $this;
    }

    /**
     * @param  callable  $callback
     * @param  array  $parameters
     * @return $this
     */
    public function hover(callable $callback, array $parameters = []): static
    {
        $this->on_hover(
            static::registerCallBack($callback, $parameters, $this->model)
        );

        return $this;
    }

    /**
     * @param  string  $title
     * @return $this
     */
    public function setTitle(string $title): static
    {
        $this->attr('title', $title);

        return $this;
    }

    /**
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
     * @param  string  $name
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * @param  string  $name
     * @return mixed
     */
    public function getAttribute(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public static function hasComponentStatic(string $name): bool
    {
        return isset(static::$components[$name]);
    }

    /**
     * @return string
     */
    public function js(): string
    {
        return <<<JS

JS;
    }

    /**
     * @return string
     */
    public function css(): string
    {
        return <<<CSS

CSS;
    }

    /**
     * @param  array  $attributes
     * @return $this
     */
    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Hide component.
     *
     * @param bool $eq
     * @return $this
     */
    public function hide(mixed $eq = true): static
    {
        if ($eq) {
            $this->attr('style', 'display: none');
        }

        return $this;
    }
}
