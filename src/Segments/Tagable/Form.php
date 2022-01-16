<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\INPUT;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\Developer\Core\Traits\Piplineble;
use Lar\LteAdmin\Segments\Tagable\Traits\BuildHelperTrait;
use Lar\LteAdmin\Segments\Tagable\Traits\FieldMassControl;
use Lar\LteAdmin\Segments\Tagable\Traits\FormAutoMakeTrait;

/**
 * Class Col
 * @package Lar\LteAdmin\Segments\Tagable
 * @macro_return Lar\LteAdmin\Segments\Tagable\FormGroup
 * @methods Lar\LteAdmin\Segments\Tagable\Field::$form_components (string $name, string $label = null, ...$params)
 * @mixin FormMethods
 * @mixin FormMacroList
 */
class Form extends \Lar\Layout\Tags\FORM {

    use FieldMassControl,
        FormAutoMakeTrait,
        Macroable,
        Piplineble,
        BuildHelperTrait,
        Delegable;

    /**
     * @var Model|null
     */
    static $current_model;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var string
     */
    protected $method = "post";

    /**
     * @var string|null
     */
    protected $action;

    /**
     * @var string
     */
    public static $last_id;

    /**
     * Form constructor.
     * @param  mixed  $model
     * @param  mixed  ...$params
     */
    public function __construct($model = null, ...$params)
    {
        if (is_embedded_call($model)) {

            $params[] = $model;

        } else {

            $this->model = $model;
        }

        if (!$this->model) {

            $this->model = gets()->lte->menu->model;
        }

        if ($this->model) {

            $this->model = static::fire_pipes($this->model, get_class($this->model));
        }

        static::$current_model = $this->model;

        parent::__construct();

        $this->when($params);

        $this->toExecute('buildForm');

        $this->callConstructEvents();
    }

    /**
     * @param  string  $method
     * @return $this
     */
    public function method(string $method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param  string  $action
     * @return $this
     */
    public function action(string $action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Form builder
     */
    protected function buildForm()
    {
        $this->callRenderEvents();

        $this->setMethod($this->method);

        $menu = gets()->lte->menu->now;

        $type = gets()->lte->menu->type;

        if (isset($menu['model.param'])) {

            $this->appEnd(
                INPUT::create(['type' => 'hidden', 'name' => '_after', 'value' => session('_after', 'index')])
            );
        }

        if (!$this->action && $type && $this->model && $menu) {

            $key = $this->model->getOriginal($this->model->getRouteKeyName());

            if ($type === 'edit' && isset($menu['link.update'])) {

                $this->action = $menu['link.update']($key);
                $this->hiddens(['_method' => 'PUT']);
            }
            else if ($type === 'create' && isset($menu['link.store'])) {

                $this->action = $menu['link.store']();
            }
        }

        else if (isset($menu['post']) && isset($menu['route']) && \Route::has($menu['route'] . '.post')) {

            $this->action = route($menu['route'] . '.post', $menu['route_params'] ?? []);
        }

        if (!$this->action) {

            $this->action = url()->current();
        }

        $this->setAction($this->action);

        $this->setEnctype('multipart/form-data');

        static::$last_id = $this->getUnique();

        $this->setId(static::$last_id);

        $this->attr('data-load', 'valid');

        static::$current_model = null;
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

            $call->setModel($this->model);

            return $call;
        }

        return parent::__call($name, $arguments);
    }

    /**
     * @param Model|Builder|string $model
     * @param  \Closure  $closure
     */
    public static function withModel($model, \Closure $closure)
    {
        $tmp_model = static::$current_model;
        static::$current_model = $model;
        $closure();
        static::$current_model = $tmp_model;
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

            if (static::$current_model) {

                $call->setModel(static::$current_model);
            }

            if (Component::$last_component) {

                Component::$last_component->appEnd($call);
            }

            return $call;
        }

        return parent::__callStatic($name, $arguments);
    }
}
