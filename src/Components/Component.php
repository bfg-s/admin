<?php

namespace Lar\LteAdmin\Components;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Components\Fields\AmountField;
use Lar\LteAdmin\Components\Fields\AutocompleteField;
use Lar\LteAdmin\Components\Fields\ChecksField;
use Lar\LteAdmin\Components\Fields\CKEditorField;
use Lar\LteAdmin\Components\Fields\CodeMirrorField;
use Lar\LteAdmin\Components\Fields\ColorField;
use Lar\LteAdmin\Components\Fields\DateField;
use Lar\LteAdmin\Components\Fields\DateRangeField;
use Lar\LteAdmin\Components\Fields\DateTimeField;
use Lar\LteAdmin\Components\Fields\DateTimeRangeField;
use Lar\LteAdmin\Components\Fields\DualSelectField;
use Lar\LteAdmin\Components\Fields\EmailField;
use Lar\LteAdmin\Components\Fields\FileField;
use Lar\LteAdmin\Components\Fields\HiddenField;
use Lar\LteAdmin\Components\Fields\IconField;
use Lar\LteAdmin\Components\Fields\ImageField;
use Lar\LteAdmin\Components\Fields\InfoCreatedAtField;
use Lar\LteAdmin\Components\Fields\InfoField;
use Lar\LteAdmin\Components\Fields\InfoIdField;
use Lar\LteAdmin\Components\Fields\InfoUpdatedAtField;
use Lar\LteAdmin\Components\Fields\InputField;
use Lar\LteAdmin\Components\Fields\MDEditorField;
use Lar\LteAdmin\Components\Fields\MultiSelectField;
use Lar\LteAdmin\Components\Fields\NumberField;
use Lar\LteAdmin\Components\Fields\NumericField;
use Lar\LteAdmin\Components\Fields\PasswordField;
use Lar\LteAdmin\Components\Fields\RadiosField;
use Lar\LteAdmin\Components\Fields\RatingField;
use Lar\LteAdmin\Components\Fields\SelectField;
use Lar\LteAdmin\Components\Fields\SelectTagsField;
use Lar\LteAdmin\Components\Fields\SwitcherField;
use Lar\LteAdmin\Components\Fields\TextareaField;
use Lar\LteAdmin\Components\Fields\TimeField;
use Lar\LteAdmin\Components\Traits\BuildHelperTrait;
use Lar\LteAdmin\Components\Traits\FieldMassControlTrait;
use Lar\LteAdmin\Core\Delegate;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Explanation;
use Lar\LteAdmin\Page;
use Lar\Tagable\Events\onRender;
use Lar\Tagable\Tag;

/**
 * @methods static::$inputs
 * @mixin ComponentMacroList
 * @mixin ComponentMethods
 */
abstract class Component extends DIV implements onRender
{
    use FieldMassControlTrait, Macroable, BuildHelperTrait, Delegable;

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
    ];

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
     * @var array|null
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

            if (is_array($model)) {
                $model = eloquent_instruction($this->model ?: $this->page->model(), $model);
            }

            if (is_string($model) && class_exists($model)) {
                $model = new $model;
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
                $class = is_string($c) ? $this->model : get_class($c);
                $this->menu = $this->page->findModelMenu($class);
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
        if ($call = $this->call_group($name, $arguments)) {
            return $call;
        }

        return parent::__call($name, $arguments);
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
}
