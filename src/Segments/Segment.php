<?php

namespace Lar\LteAdmin\Segments;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\Traits\BuildHelperTrait;
use Lar\LteAdmin\Segments\Tagable\Traits\FieldMassControl;
use Lar\Tagable\Events\onRender;

abstract class Segment extends DIV implements onRender
{
    use FieldMassControl, Macroable, BuildHelperTrait;

    /**
     * @var Builder|Model|Relation|null
     */
    protected $model = null;

    /**
     * @var string|null
     */
    protected $model_name = null;

    /**
     * Has models on process
     * @var array
     */
    static protected $models = [];

    public function __construct(...$params)
    {
        parent::__construct();

        $this->model_name = $this->getModelName();

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
     * @return mixed|void
     * @throws \ReflectionException
     */
    public function onRender()
    {
        $this->mount();
        $this->callRenderEvents();
    }

    /**
     * Component mount method
     * @return void
     */
    abstract protected function mount ();

    /**
     * For construct params.
     * if you what's expect of model or relation parameter
     * @param  array  $params
     * @return array
     */
    protected function expectModel(array $params)
    {
        $model = null;
        if (isset($params[0]) && !is_embedded_call($params[0])) {
            $model = $params[0];
            unset($params[0]);
        }

        if (!$model && Form::$current_model) {

            $model = Form::$current_model;
        }

        if (!$model) {
            $model = gets()->lte->menu->model;
        }

        $this->model = $model;

        return $params;
    }

    /**
     * @param $model
     * @return string|false
     */
    public function getModelName()
    {
        if ($this->model_name) { return $this->model_name; }
        $class = null;
        if ($this->model instanceof Model) { $class = get_class($this->model); }
        else if ($this->model instanceof Builder) { $class = get_class($this->model->getModel()); }
        else if ($this->model instanceof Relation) { $class = get_class($this->model->getModel()); }
        else if (is_object($this->model)) { $class = get_class($this->model); }
        else if (is_string($this->model)) { $class = $this->model; }
        else if (is_array($this->model)) { $class = substr(md5(json_encode($this->model)), 0, 10); }
        $this->model_class = $class;
        $return = $class ? strtolower(class_basename($class)) : $this->getUnique();
        $prep = "";
        if (isset(static::$models[$return])) {
            $prep .= static::$models[$return];
            static::$models[$return]++;
        } else {
            static::$models[$return] = 1;
        }
        return $return.$prep;
    }
}
