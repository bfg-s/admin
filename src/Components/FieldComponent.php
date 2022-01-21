<?php

namespace Lar\LteAdmin\Components;

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
use Lar\LteAdmin\Components\Fields\InfoField;
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
use Lar\LteAdmin\Components\Traits\FieldMassControlTrait;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\Tagable\Events\onRender;

/**
 * @macro_return Lar\LteAdmin\Components\FormGroup
 * @methods static::$form_components (string $name, string $label = null, ...$params)
 * @mixin FieldComponentMethods
 * @mixin FieldComponentMacroList
 */
class FieldComponent extends DIV implements onRender
{
    use FieldMassControlTrait, Macroable, Delegable;

    /**
     * @var array
     */
    public static $form_components = [
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
        'rating' => RatingField::class,
        'hidden' => HiddenField::class,
        'autocomplete' => AutocompleteField::class,
    ];

    /**
     * @var bool
     */
    protected $only_content = true;

    protected $label = null;

    /**
     * Fields constructor.
     * @param  mixed  ...$params
     */
    public function __construct(...$params)
    {
        parent::__construct();

        $this->when($params);

        $this->callConstructEvents();
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|FormComponent|\Lar\Tagable\Tag|mixed|string
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if ($call = $this->call_group($name, $arguments)) {
            return $call;
        }

        return parent::__call($name, $arguments);
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|FieldComponent|FormGroupComponent|mixed
     * @throws \Exception
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
        static::$form_components[$name] = $class;
    }

    /**
     * @param  array  $array
     */
    public static function mergeFormComponents(array $array)
    {
        static::$form_components = array_merge(static::$form_components, $array);
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public static function has(string $name)
    {
        return isset(static::$form_components[$name]);
    }

    /**
     * @return mixed|void
     * @throws \ReflectionException
     */
    public function onRender()
    {
        $this->callRenderEvents();
    }
}
