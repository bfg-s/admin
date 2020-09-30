<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ViewErrorBag;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\I;
use Lar\Layout\Traits\FontAwesome;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Segments\Tagable\Traits\RulesBackTrait;
use Lar\LteAdmin\Segments\Tagable\Traits\RulesFrontTrait;

/**
 * Class Col
 * @package Lar\LteAdmin\Segments\Tagable
 * @mixin FormGroupMacroList
 */
abstract class FormGroup extends DIV {

    use RulesFrontTrait, RulesBackTrait, FontAwesome, Macroable;

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
     * @var bool
     */
    protected $reversed = false;

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
     * @var string
     */
    protected $field_id;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var bool
     */
    protected $has_bug = false;

    /**
     * @var ViewErrorBag
     */
    protected $errors;

    /**
     * @var bool
     */
    protected $admin_controller = false;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var bool
     */
    protected $only_input = false;

    /**
     * @var \Closure
     */
    protected $value_to;

    /**
     * @var array
     */
    static $construct_modify = [];

    /**
     * FormGroup constructor.
     * @param  Component  $parent
     * @param  string  $title
     * @param  string  $name
     * @param  mixed  ...$params
     */
    public function __construct(string $name, string $title = null, ...$params)
    {
        parent::__construct();

        $this->title = $title ? __($title) : $title;
        $this->name = $name;
        $this->params = array_merge($this->params, $params);
        $this->field_id = 'input_' . \Str::slug($this->name, '_');
        $this->path = trim(str_replace(['[',']'], '.', str_replace('[]', '', $name)), '.');
        $this->errors = request()->session()->get('errors') ?: new ViewErrorBag;
        $this->has_bug = $this->errors->getBag('default')->has($name);
        if (\Route::current()) {
            list($this->controller, $this->method) = \Str::parseCallback(\Route::currentRouteAction());
            $this->admin_controller = property_exists($this->controller, 'permission_functions');
        }
        $this->toExecute('makeWrapper');
        if (!$title) { $this->vertical(); }
        $this->model = Form::$current_model;
        $this->after_construct();
        $this->callConstructEvents();
        foreach (FormGroup::$construct_modify as $item) {
            if (is_callable($item)) {
                call_user_func($item, $this, $this->model);
            }
        }
    }

    /**
     * @param  Component  $parent
     * @return $this
     */
    public function set_parent(Component $parent)
    {
        $this->parent_field = $parent;

        return $this;
    }

    /**
     * After construct event
     */
    protected function after_construct(){}

    /**
     * @return $this
     */
    public function vertical()
    {
        $this->vertical = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function reversed()
    {
        $this->reversed = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function only_input()
    {
        $this->only_input = true;

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

    public function crypt()
    {
        if ($this->admin_controller) {
            $this->controller::$crypt_fields[] = $this->name;
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
     * @param  mixed  ...$values
     * @return $this
     */
    public function value(...$values)
    {
        $this->value = implode(' ', $values);

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
     * @return void
     */
    protected function on_build() {}

    /**
     * Make wrapper for input
     */
    protected function makeWrapper()
    {
        $this->callRenderEvents();

        $this->on_build();

        if ($this->only_input) {

            $this->value = $this->create_value();

            $this->appEnd(
                $this->field()
            )->appEnd(
                $this->app_end_field()
            );

            return ;
        }

        $fg = $this->div(['form-group row']);

        if (!$this->reversed) {

            $this->make_label($fg);
        }

        $icon = is_string($this->icon) && preg_match('/^(fas\s|fab\s|far\s)fa\-[a-zA-Z0-9\-\_]+/', $this->icon) ?
            I::create([$this->icon]) : $this->icon;

        $group_width = 12 - $this->label_width;
        $input_group = $fg->div()->addClassIf($icon, 'input-group')
            ->addClassIf($this->vertical, 'w-100')
            ->addClassIf(!$this->vertical && $this->title, "col-sm-{$group_width}");

        $this->make_icon_wrapper($input_group, $icon);

        $fg->setDatas(['label-width' => $this->label_width]);

        if ($this->vertical) {

            $fg->setDatas(['vertical' => 'true']);
        }

        $this->value = $this->create_value();

        if ($this->value_to) {

            $this->value = call_user_func($this->value_to, $this->value);
        }

        $input_group->appEnd(
            $this->field()
        )->appEnd(
            $this->app_end_field()
        );

        if ($this->reversed) {

            $this->make_label($fg);
        }

        $this->make_info_message($fg)
            ->make_error_massages($fg);
    }

    /**
     * @param  \Closure|array  $call
     * @return $this
     */
    public function value_to($call)
    {
        $this->value_to = $call;

        return $this;
    }

    /**
     * @return mixed
     */
    protected function create_value () {

        return old($this->path, ($this->model ? (multi_dot_call($this->model, $this->path) ?? $this->value): $this->value));
    }

    /**
     * @return string
     */
    protected function app_end_field()
    {
        return '';
    }

    /**
     * @param  DIV  $form_group
     */
    protected function make_label(DIV $form_group)
    {
        if ($this->title) {

            $form_group->label(['for' => $this->field_id, 'class' => 'col-form-label'], $this->title)
                ->addClassIf(!$this->vertical, 'col-sm-'.$this->label_width);
        }
    }

    /**
     * @param  DIV  $input_group
     * @param mixed $icon
     * @return $this
     */
    protected function make_icon_wrapper(DIV $input_group, $icon = null)
    {
        if ($icon) {

            $input_group->div(['class' => 'input-group-prepend'])
                ->span(['class' => 'input-group-text'], $icon);
        }

        return $this;
    }

    /**
     * @param  DIV  $fg
     * @return $this
     */
    protected function make_info_message(DIV $fg)
    {
        if ($this->info) {

            $group_width = 12 - $this->label_width;

            if (!$this->vertical) {
                $fg->div(["col-sm-{$this->label_width}"]);
            }
            $fg->small(['text-primary invalid-feedback d-block'])
                ->addClassIf(!$this->vertical, "col-sm-{$group_width}")
                ->i(['fas fa-info-circle'])->_text(":space", $this->info);
        }

        return $this;
    }

    /**
     * @param  DIV  $fg
     * @return $this
     */
    protected function make_error_massages(DIV $fg)
    {
        if ($this->name && $this->errors && $this->errors->has($this->name)) {

            $group_width = 12 - $this->label_width;

            $messages = $this->errors->get($this->name);

            foreach ($messages as $mess) {

                if (!$this->vertical) {
                    $fg->div(["col-sm-{$this->label_width}"]);
                }
                $fg->small(['error invalid-feedback d-block'])
                    ->addClassIf(!$this->vertical, "col-sm-{$group_width}")
                    ->small(['fas fa-exclamation-triangle'])->_text(":space", $mess);
            }
        }

        return $this;
    }

    /**
     * @param  string  $var
     * @return $this
     */
    public function stated(string $var = null)
    {
        $this->data['stated'] = $var ? $var : '';
        $this->data['state'] = $var ? $var : '';

        return $this;
    }

    /**
     * @param  string|null  $var
     * @return $this
     */
    public function state(string $var = null)
    {
        $this->data['state'] = $var ? $var : '';

        return $this;
    }

    /**
     * @return string
     */
    public function get_id()
    {
        return $this->field_id;
    }

    /**
     * @param $id
     * @return $this
     */
    public function set_id($id)
    {
        $this->field_id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @param  string  $name
     * @return $this
     */
    public function set_name(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return \Lar\Layout\Tags\INPUT|mixed
     */
    abstract public function field();
}