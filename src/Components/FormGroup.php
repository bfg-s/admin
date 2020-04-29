<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\DIV;

class FormGroup extends DIV
{
    /**
     * @var array
     */
    protected $props = [
        'form-group row'
    ];

    /**
     * @var bool
     */
    public $opened_mode = true;

    /**
     * @var string
     */
    private $__value = null;

    /**
     * @var string
     */
    private $__name = '';

    /**
     * @var string
     */
    private $__path = '';

    /**
     * @var string
     */
    private $__title = '';

    /**
     * @var string
     */
    private $__id = '';

    /**
     * @var boolean
     */
    public $__v = false;

    /**
     * @var array
     */
    protected static $iterators = [];

    /**
     * @var bool
     */
    public static $vertical = false;

    /**
     * @var bool
     */
    public static $model;

    /**
     * Col constructor.
     * @param  string|array  $label
     * @param  string  $name
     * @param  string|bool  $icon
     * @param  string|null  $info
     * @param  mixed  ...$params
     */
    public function __construct($label, string $name, $icon = 'fas fa-pencil-alt', string $info = null, ...$params)
    {
        parent::__construct();

        //$model = static::$model ?? gets()->lte->menu->model;

        $this->when($params);

        $this->__v = static::$vertical;

        $this->__id = 'input_' . \Str::slug($name, '_');

        if ($label) {

            $label_obj = $this->label(['for' => $this->__id, 'class' => 'col-form-label'], $label)->addClassIf(!$this->__v, 'col-sm-2');

            if ($label_obj) {

                $label_obj->text("<small class='form-text text-muted form-info-text'>{$info}</small>");
            }
        }

        $inner = is_string($icon) && preg_match('/^(fas\s|fab\s|far\s)fa\-[a-zA-Z0-9\-\_]+/', $icon) ?
                "<i class='{$icon}'></i>" : $icon;

        $div1 = $this->div(['class' => ($inner ? 'input-group ':'').($this->__v ? '' : ($label ? 'col-sm-10' : ''))])->openMode();

        if ($inner) {
            $div1->div(['class' => 'input-group-prepend'])
                ->span(['class' => 'input-group-text'], $inner);
        }

        if (!isset(static::$iterators[$name])) static::$iterators[$name] = 0;
        else static::$iterators[$name]++;

        $this->__path = trim(str_replace(['[',']'], '.', str_replace('[]', '', $name)), '.');
        $this->__value = static::$model ? multi_dot_call(static::$model, $this->__path) : null;
        $this->__name = $name;
        $this->__title = $label ?? $name;

        static::$vertical = false;
        static::$model = null;
    }

    /**
     * @return string
     */
    public function __getName()
    {
        return $this->__name;
    }

    /**
     * @return string
     */
    public function __getTitle()
    {
        return $this->__title;
    }

    /**
     * @return string
     */
    public function __getId()
    {
        return $this->__id;
    }

    /**
     * @return string
     */
    public function __getValue()
    {
        return $this->__value;
    }

    /**
     * @return string
     */
    public function __getPath()
    {
        return $this->__path;
    }
}