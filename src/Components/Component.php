<?php

namespace Admin\Components;

use Admin\Components\Fields\SliderField;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Lar\Layout\Tags\DIV;
use Lar\Tagable\Events\onRender;
use Lar\Tagable\Tag;
use Admin\Components\Fields\AmountField;
use Admin\Components\Fields\AutocompleteField;
use Admin\Components\Fields\ChecksField;
use Admin\Components\Fields\CKEditorField;
use Admin\Components\Fields\CodeMirrorField;
use Admin\Components\Fields\ColorField;
use Admin\Components\Fields\DateField;
use Admin\Components\Fields\DateRangeField;
use Admin\Components\Fields\DateTimeField;
use Admin\Components\Fields\DateTimeRangeField;
use Admin\Components\Fields\DualSelectField;
use Admin\Components\Fields\EmailField;
use Admin\Components\Fields\FileField;
use Admin\Components\Fields\HiddenField;
use Admin\Components\Fields\IconField;
use Admin\Components\Fields\ImageField;
use Admin\Components\Fields\InfoCreatedAtField;
use Admin\Components\Fields\InfoField;
use Admin\Components\Fields\InfoIdField;
use Admin\Components\Fields\InfoUpdatedAtField;
use Admin\Components\Fields\InputField;
use Admin\Components\Fields\MDEditorField;
use Admin\Components\Fields\MultiSelectField;
use Admin\Components\Fields\NumberField;
use Admin\Components\Fields\NumericField;
use Admin\Components\Fields\PasswordField;
use Admin\Components\Fields\RadiosField;
use Admin\Components\Fields\RatingField;
use Admin\Components\Fields\SelectField;
use Admin\Components\Fields\SelectTagsField;
use Admin\Components\Fields\SwitcherField;
use Admin\Components\Fields\TextareaField;
use Admin\Components\Fields\TimeField;
use Admin\Controllers\Controller;
use Admin\Core\Delegate;
use Admin\Core\MenuItem;
use Admin\Explanation;
use Admin\Jax\Admin;
use Admin\Page;
use Admin\Traits\BuildHelperTrait;
use Admin\Traits\Delegable;
use Admin\Traits\FieldMassControlTrait;
use Admin\Traits\Macroable;

/**
 * @methods static::$inputs
 * @mixin ComponentMacroList
 * @mixin ComponentMethods
 */
abstract class Component extends DIV implements onRender
{
    use FieldMassControlTrait;
    use Macroable;
    use BuildHelperTrait;
    use Delegable;

    /**
     * @var array
     */
    public static $inputs = [
        'input' => InputField::class,
        'password' => PasswordField::class,
        'email' => EmailField::class,
        'number' => NumberField::class,
        'numeric' => NumericField::class,
        'amount' => AmountField::class,
        'file' => FileField::class,
        'image' => ImageField::class,
        'switcher' => SwitcherField::class,
        'date_range' => DateRangeField::class,
        'date_time_range' => DateTimeRangeField::class,
        'date' => DateField::class,
        'date_time' => DateTimeField::class,
        'time' => TimeField::class,
        'icon' => IconField::class,
        'color' => ColorField::class,
        'select' => SelectField::class,
        'dual_select' => DualSelectField::class,
        'multi_select' => MultiSelectField::class,
        'select_tags' => SelectTagsField::class,
        'textarea' => TextareaField::class,
        'ckeditor' => CKEditorField::class,
        'mdeditor' => MDEditorField::class,
        'checks' => ChecksField::class,
        'radios' => RadiosField::class,
        'codemirror' => CodeMirrorField::class,
        'info' => InfoField::class,
        'info_id' => InfoIdField::class,
        'info_created_at' => InfoCreatedAtField::class,
        'info_updated_at' => InfoUpdatedAtField::class,
        'rating' => RatingField::class,
        'hidden' => HiddenField::class,
        'autocomplete' => AutocompleteField::class,
        'slider' => SliderField::class,
    ];
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
    protected $menu;
    protected $iSelectModel = false;

    /**
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        $this->page = app(Page::class);

        parent::__construct();

        $this->model();
        $this->iSelectModel = false;

        $this->delegates(...$delegates);

        if ($this->class) {
            $this->addClass($this->class);
        }

        $this->callConstructEvents();
    }

    /**
     * @param $model
     * @return $this
     */
    public function model($model = null)
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
            $this->model_name = $this->page->getModelName($model);
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

    public function realModel()
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
    public function delegates(...$delegates)
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
     * @return bool|FieldComponent|FormGroupComponent|mixed
     * @throws Exception
     */
    public static function __callStatic($name, $arguments)
    {
        if ($call = static::static_call_group($name, $arguments)) {
            return $call;
        }

        return parent::__callStatic($name, $arguments);
    }

    /**
     * @param  string  $name
     * @param  string  $class
     */
    public static function registerFormComponent(string $name, string $class)
    {
        static::$inputs[$name] = $class;
    }

    /**
     * @param  array  $array
     */
    public static function mergeFormComponents(array $array)
    {
        static::$inputs = array_merge(static::$inputs, $array);
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public static function has(string $name)
    {
        return isset(static::$inputs[$name]);
    }

    /**
     * @param ...$delegates
     * @return $this
     */
    public function forceDelegates(...$delegates)
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
    public function forceDelegateNow(...$delegates)
    {
        $this->newExplainForce($delegates);

        return $this;
    }

    public function realModelClass()
    {
        $model = $this->realModel();

        return $model ? get_class($model) : null;
    }

    public function withCollection($collection, callable $callback)
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
    public function with(callable $callback)
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
    public function delegatesNow(...$delegates)
    {
        $this->newExplain($delegates);

        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|FormComponent|Tag|mixed|string
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (!Component::$regInputs) {
            $inputs = Component::$regInputs = implode('|', array_keys(Component::$inputs));
        } else {
            $inputs = Component::$regInputs;
        }

        if (
            preg_match("/^($inputs)_(.+)$/", $name, $matches)
            && !isset(Component::$inputs[$name])
            && !Controller::hasExplanation($name)
        ) {
            $field = $matches[1];
            $name = str_replace(['_dot_', '__'], '.', Str::snake($matches[2], '_'));
            $label = $arguments[0] ?? ucfirst(str_replace(['.', '_'], ' ', $name));

            return $this->{$field}($name, Lang::has("admin.$label") ? __("admin.$label") : $label);
        } else {
            if ($call = $this->call_group($name, $arguments)) {
                return $call;
            }
        }

        return parent::__call($name, $arguments);
    }

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
            && !Controller::hasExplanation($name)
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
     * @return mixed|void
     */
    public function onRender()
    {
        $this->newExplain($this->delegates);
        $this->newExplainForce($this->force_delegates);
        if (!$this->iSelectModel && ($this->parent?->model ?? null)) {
            $this->model($this->parent->model);
        }
        $this->mount();
        $this->callRenderEvents();
    }

    /**
     * Component mount method.
     * @return void
     */
    abstract protected function mount();

    public function click(callable $callback, array $parameters = []): static
    {
        $this->on_click(
            static::registerCallBack($callback, $parameters, $this->model)
        );

        return $this;
    }

    public static function registerCallBack(callable $callback, array $parameters = [], $model = null)
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

    public function dblclick(callable $callback, array $parameters = []): static
    {
        $this->on_dblclick(
            static::registerCallBack($callback, $parameters, $this->model)
        );

        return $this;
    }

    public function hover(callable $callback, array $parameters = []): static
    {
        $this->on_hover(
            static::registerCallBack($callback, $parameters, $this->model)
        );

        return $this;
    }
}
