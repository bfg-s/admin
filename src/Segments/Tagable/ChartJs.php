<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Core\Traits\Delegable;
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

    use FieldMassControl, Macroable, BuildHelperTrait, Delegable;

    /**
     * @var ChartJsBuilder
     */
    public $builder;

    protected $model;
    protected $dataBuilder;

    static protected $count = 0;

    protected $size = 100;
    protected $type = 'line';
    protected $datasets = [];

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

    public function size(int $size)
    {
        $this->size = $size;
        return $this;
    }

    public function type(int $type)
    {
        $this->type = $type;
        return $this;
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






    public function prepareData(callable $callback = null): ChartJs
    {
        if (!$this->dataBuilder) {
            $this->dataBuilder = $this->model;
        }
        if ($callback) {
            $result = call_user_func($callback, $this->dataBuilder);
            if ($result) {
                $this->dataBuilder = $result;
            }
        }

        return $this;
    }

    public function setDataBetween(string $column, $from, $to)
    {
        return $this->prepareData(function ($model) use ($column, $from, $to) {
            return $model->whereBetween($column, [$from, $to]);
        });
    }



    public function groupDataByAt(string $atColumn, string $format = 'Y.m.d')
    {
        return $this->prepareData(function ($model) use ($atColumn, $format) {
            return $model->get()->groupBy(function (Model $model) use ($atColumn, $format) {
                return ($model->{$atColumn} instanceof Carbon ? $model->{$atColumn} : Carbon::parse($model->{$atColumn}))->format($format);
            });
        });
    }

    public function groupDataBy(string $column)
    {
        return $this->prepareData(function ($model) use ($column) {
            return $model->get()->groupBy(function (Model $model) use ($column) {
                return $model->{$column};
            });
        });
    }



    public function eachPointCount(string $title)
    {
        return $this->eachPoint($title, function (Collection $collection) {
            return $collection->count();
        });
    }

    public function eachPointSum(string $title, $callback = null)
    {
        return $this->eachPoint($title, function (Collection $collection) use ($callback) {
            return $collection->sum($callback);
        });
    }

    public function eachPoint(string $title, $callback = null, $default = 0)
    {
        $this->datasets[] = [
            'title' => $title,
            'data' => collect($this->dataBuilder)->map(function ($collection) use ($callback, $default) {
                return $collection instanceof Collection ? call_user_func($callback, $collection) : $default;
            })
        ];

        return $this;
    }



    public function miniChart()
    {
        $this->builder
            ->type($this->type)
            ->size(['width' => 400, 'height' => $this->size])
            ->labels(collect($this->dataBuilder)->keys()->toArray());

        foreach ($this->datasets as $dataset) {
            $bgColor = $this->randColor();
            $this->builder->addDataset(
                [
                    "label" => __($dataset['title']),
                    'backgroundColor' => $this->renderColor($bgColor, '0.31'), //"rgba(38, 185, 154, 0.31)",
                    'borderColor' => $this->renderColor($bgColor, '0.7'), //"rgba(38, 185, 154, 0.7)",
                    "pointBorderColor" => $this->renderColor($bgColor, '0.7'), //"rgba(38, 185, 154, 0.7)",
                    "pointBackgroundColor" => $this->renderColor($bgColor, '0.7'), //"rgba(38, 185, 154, 0.7)",
                    "pointHoverBackgroundColor" => $this->randColor(), //"#fff",
                    "pointHoverBorderColor" => $this->randColor(),
                    'data' => collect($dataset['data'])->values()->toArray(),
                ]
            );
        }
        return $this;
    }

    protected function randColor(int $min = 1, $max = 255)
    {
        $r1 = rand($min,$max); $r2 = rand($min,$max); $r3 = rand($min,$max);
        return [$r1, $r2, $r3];
    }

    protected function renderColor($c, $opacity)
    {
        return "rgba({$c[0]}, {$c[1]}, {$c[2]}, $opacity)";
    }
}
