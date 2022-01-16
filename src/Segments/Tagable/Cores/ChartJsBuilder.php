<?php

namespace Lar\LteAdmin\Segments\Tagable\Cores;

use Illuminate\Support\Arr;

class ChartJsBuilder
{
    /**
     * @var array
     */
    static protected $charts = [];

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $defaults = [
        'datasets' => [],
        'labels'   => [],
        'type'     => 'line',
        'options'  => [],
        'size'     => ['width' => null, 'height' => null]
    ];

    /**
     * @var array
     */
    private $types = [
        'bar',
        'horizontalBar',
        'bubble',
        'scatter',
        'doughnut',
        'line',
        'pie',
        'polarArea',
        'radar'
    ];

    public function __construct()
    {
        $num = count(static::$charts);
        $this->name("chart$num");
    }

    /**
     * @param $name
     *
     * @return $this|ChartJsBuilder
     */
    public function name($name)
    {
        $old = static::$charts[$this->name] ?? [];
        $this->name = $name;
        static::$charts[$name] = array_merge($this->defaults, $old);
        return $this;
    }

    /**
     * @param $element
     *
     * @return ChartJsBuilder
     */
    public function element($element)
    {
        return $this->set('element', $element);
    }

    /**
     * @param array $labels
     *
     * @return ChartJsBuilder
     */
    public function labels(array $labels)
    {
        return $this->set('labels', $labels);
    }

    /**
     * @param array $datasets
     *
     * @return ChartJsBuilder
     */
    public function datasets(array $datasets)
    {
        return $this->set('datasets', $datasets);
    }

    /**
     * @param array $datasets
     *
     * @return ChartJsBuilder
     */
    public function simpleDatasets(string $label, array $dataset)
    {
        static::$charts[$this->name]['datasets'][] = [
            "label" => $label,
            'data' => $dataset,
        ];

        return $this;
    }

    public function addDataset(array $dataset)
    {
        static::$charts[$this->name]['datasets'][] = $dataset;

        return $this;
    }

    /**
     * @param $type
     *
     * @return ChartJsBuilder
     */
    public function type($type)
    {
        if (!in_array($type, $this->types)) {
            throw new \InvalidArgumentException('Invalid Chart type.');
        }
        return $this->set('type', $type);
    }

    /**
     * @param array $size
     *
     * @return ChartJsBuilder
     */
    public function size($size)
    {
        return $this->set('size', $size);
    }

    /**
     * @param array $options
     *
     * @return $this|ChartJsBuilder
     */
    public function options(array $options)
    {
        foreach ($options as $key => $value) {
            $this->set('options.' . $key, $value);
        }

        return $this;
    }

    /**
     *
     * @param string|array $optionsRaw
     * @return \self
     */
    public function optionsRaw($optionsRaw)
    {
        if (is_array($optionsRaw)) {
            $this->set('optionsRaw', json_encode($optionsRaw, true));
            return $this;
        }

        $this->set('optionsRaw', $optionsRaw);
        return $this;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $chart = static::$charts[$this->name];

        return view('lte::segment.chartjs')
            ->with('isNotAjax', !request()->ajax() && !request()->pjax())
            ->with('datasets', $chart['datasets'])
            ->with('element', $this->name)
            ->with('labels', $chart['labels'])
            ->with('options', $chart['options'] ?? '')
            ->with('optionsRaw', $chart['optionsRaw'] ?? '')
            ->with('type', $chart['type'])
            ->with('size', $chart['size']);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    private function get($key)
    {
        return Arr::get(static::$charts[$this->name], $key);
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this|ChartJsBuilder
     */
    private function set($key, $value)
    {
        Arr::set(static::$charts[$this->name], $key, $value);

        return $this;
    }
}
