<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Segments\Tagable\Cores\ChartJsBuilder;
use Lar\LteAdmin\Segments\Tagable\Traits\BuildHelperTrait;
use Lar\LteAdmin\Segments\Tagable\Traits\FieldMassControl;
use Lar\Tagable\Events\onRender;

/**
 * Class ChartJs
 * @package Lar\LteAdmin\Segments\Tagable
 * @methods Lar\LteAdmin\Segments\Tagable\Field::$form_components (string $name, string $label = null, ...$params)
 * @mixin ChartJsMacroList
 * @mixin ChartJsMethods
 */
class ChartJs extends DIV implements onRender {

    use FieldMassControl, Macroable, BuildHelperTrait;

    /**
     * @var ChartJsBuilder
     */
    public $builder;

    protected $model;

    static protected $count = 0;

    /**
     * @param $callback
     * @param ...$params
     * @throws \Throwable
     */
    public function __construct($model = null, $callback = null, ...$params)
    {
        static::$count++;

        parent::__construct();

        if (is_callable($model)) {
            $callback = $model;
            $model = null;
        }

        $this->model = $model;

        if (!$this->model) {

            $this->model = gets()->lte->menu->model;

        } else if (is_string($this->model)) {

            $this->model = new $this->model;
        }

        $this->builder = new ChartJsBuilder();

        if ($this->model) {

            $this->builder->name(strtolower(str_replace('\\', '_', $this->model::class))."_".static::$count);
        }

        if (is_callable($callback)) {
            embedded_call($callback, [
                static::class => $this,
                ChartJsBuilder::class => $this->builder
            ]);
        } else if ($callback) {
            $params[] = $callback;
        }

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
        $this->text(
            $this->builder->render()
        );
        $this->callRenderEvents();
    }
}
