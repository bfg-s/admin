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
     * @var string|null
     */
    private $__info;

    /**
     * @var int
     */
    private $__label_width;

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
     * @param  int  $label_width
     * @param  mixed  ...$params
     */
    public function __construct($label, string $name, $icon = 'fas fa-pencil-alt', string $info = null, int $label_width = 2, ...$params)
    {
        parent::__construct();

        if ($icon === true) {$icon = 'fas fa-pencil-alt';}

        $this->when($params);

        $this->__v = static::$vertical;
        $this->__v = $label ? $this->__v : true;

        $this->__id = 'input_' . \Str::slug($name, '_');

        if ($label) {

            $this->label(['for' => $this->__id, 'class' => 'col-form-label'], $label)->addClassIf(!$this->__v, 'col-sm-'.$label_width);
        }

        $inner = is_string($icon) && preg_match('/^(fas\s|fab\s|far\s)fa\-[a-zA-Z0-9\-\_]+/', $icon) ?
                "<i class='{$icon}'></i>" : $icon;

        $div1 = $this->div(['class' => ($inner ? 'input-group ':'').($this->__v ? 'w-100' : ($label ? 'col-sm-'.(12-$label_width) : ''))])->openMode();

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
        $this->__info = $info;
        $this->__label_width = $label_width;

        if ($this->__v) {
            $this->setDatas(['vertical' => 'true']);
        }

        $this->setDatas(['label-width' => $label_width]);

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

    /**
     * @return string
     */
    public function __getInfo()
    {
        return $this->__info;
    }

    /**
     * @return string
     */
    public function __labelWidth()
    {
        return $this->__label_width;
    }
}