<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Segments\Tagable\Fields\Checks;
use Lar\LteAdmin\Segments\Tagable\Fields\CKEditor;
use Lar\LteAdmin\Segments\Tagable\Fields\Color;
use Lar\LteAdmin\Segments\Tagable\Fields\Date;
use Lar\LteAdmin\Segments\Tagable\Fields\DateRange;
use Lar\LteAdmin\Segments\Tagable\Fields\DateTime;
use Lar\LteAdmin\Segments\Tagable\Fields\DateTimeRange;
use Lar\LteAdmin\Segments\Tagable\Fields\DualSelect;
use Lar\LteAdmin\Segments\Tagable\Fields\Email;
use Lar\LteAdmin\Segments\Tagable\Fields\File;
use Lar\LteAdmin\Segments\Tagable\Fields\Icon;
use Lar\LteAdmin\Segments\Tagable\Fields\Image;
use Lar\LteAdmin\Segments\Tagable\Fields\Input;
use Lar\LteAdmin\Segments\Tagable\Fields\MDEditor;
use Lar\LteAdmin\Segments\Tagable\Fields\MultiSelect;
use Lar\LteAdmin\Segments\Tagable\Fields\Number;
use Lar\LteAdmin\Segments\Tagable\Fields\Password;
use Lar\LteAdmin\Segments\Tagable\Fields\Radios;
use Lar\LteAdmin\Segments\Tagable\Fields\Select;
use Lar\LteAdmin\Segments\Tagable\Fields\SelectTags;
use Lar\LteAdmin\Segments\Tagable\Fields\Switcher;
use Lar\LteAdmin\Segments\Tagable\Fields\Textarea;
use Lar\LteAdmin\Segments\Tagable\Fields\Time;
use Lar\LteAdmin\Segments\Tagable\Traits\FieldMassControl;
use Lar\Tagable\Events\onRender;

/**
 * Class Col
 * @package Lar\LteAdmin\Segments\Tagable
 * @mixin \Lar\LteAdmin\Core\FormGroupComponents
 */
class Field extends DIV implements onRender {

    use FieldMassControl;

    /**
     * @var array
     */
    static $form_components = [
        'input' => Input::class,
        'password' => Password::class,
        'email' => Email::class,
        'number' => Number::class,
        'file' => File::class,
        'image' => Image::class,
        'switcher' => Switcher::class,
        'date_range' => DateRange::class,
        'date_time_range' => DateTimeRange::class,
        'date' => Date::class,
        'date_time' => DateTime::class,
        'time' => Time::class,
        'icon' => Icon::class,
        'color' => Color::class,
        'select' => Select::class,
        'dual_select' => DualSelect::class,
        'multi_select' => MultiSelect::class,
        'select_tags' => SelectTags::class,
        'textarea' => Textarea::class,
        'ckeditor' => CKEditor::class,
        'mdeditor' => MDEditor::class,
        'checks' => Checks::class,
        'radios' => Radios::class,
    ];

    /**
     * @var bool
     */
    protected $only_content = true;

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
     * @return bool|Form|\Lar\Tagable\Tag|mixed|string
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
     * @return bool|Field|FormGroup|mixed
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