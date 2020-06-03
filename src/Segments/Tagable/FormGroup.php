<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\Traits\FontAwesome;
use Lar\LteAdmin\Segments\Tagable\Traits\FormGroupRulesTrait;

/**
 * Class Col
 * @package Lar\LteAdmin\Segments\Tagable
 */
abstract class FormGroup extends DIV {

    use FormGroupRulesTrait, FontAwesome;

    /**
     * @var bool
     */
    protected $only_content = true;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $icon = "fas fa-pencil-alt";

    /**
     * @var string
     */
    protected $info;

    /**
     * @var int
     */
    protected $label_width = 2;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var bool
     */
    protected $vertical = false;

    /**
     * @var Component|Form
     */
    protected $parent_field;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * FormGroup constructor.
     * @param  Component  $parent
     * @param  string  $title
     * @param  string  $name
     * @param  mixed  ...$params
     */
    public function __construct(Component $parent, string $name, string $title = null, ...$params)
    {
        parent::__construct();

        $this->title = $title;
        $this->name = $name;
        $this->params = array_merge($this->params, $params);
        $this->parent_field = $parent;

        $this->toExecute('makeWrapper');
    }

    /**
     * Make wrapper for input
     */
    protected function makeWrapper()
    {
        $this->view('lte::wrapper.form_group', [
            'set_title' => $this->title,
            'set_name' => $this->name,
            'set_icon' => $this->icon,
            'set_info' => $this->info,
            'set_label_width' => $this->label_width,
            'set_params' => $this->params,
            'vertical' => $this->vertical,
            'model' => $this->model,
            'form_group' => function () {return $this;}
        ]);
    }

    /**
     * @return $this
     */
    public function vertical()
    {
        $this->vertical = true;

        return $this;
    }

    /**
     * @param  string  $icon
     * @return $this
     */
    public function icon(string $icon)
    {
        if ($this->icon !== null) {

            $this->icon = $icon;
        }

        return $this;
    }

    /**
     * @param  int  $width
     * @return $this
     */
    public function label_width(int $width)
    {
        $this->label_width = $width;

        return $this;
    }

    /**
     * @param  array  $datas
     * @return $this
     */
    public function mergeDataList(array $datas)
    {
        $this->data = array_merge($this->data, $datas);

        return $this;
    }

    /**
     * @param  array  $rules
     * @return $this
     */
    public function mergeRuleList(array $rules)
    {
        $this->rules = array_merge($this->rules, $rules);

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function default($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param  Model  $model
     * @return $this
     */
    public function setModel(Model $model = null)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param  string  $info
     * @return $this
     */
    public function info(string $info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * @param  string  $name
     * @param  string  $title
     * @param  string  $id
     * @param  null  $value
     * @param  bool  $has_bug
     * @param  null  $path
     * @return \Lar\Layout\Tags\INPUT|mixed
     */
    abstract public function field(string $name, string $title, string $id = '', $value = null, bool $has_bug = false, $path = null);
}