<?php

namespace Admin\Components\Cores;

use Illuminate\Support\Arr;
use InvalidArgumentException;

class ChartJsComponentCore
{
    /**
     * @var array
     */
    protected static $charts = [];

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $defaults = [
        'datasets' => [],
        'labels' => [],
        'type' => 'line',
        'options' => [],
        'size' => ['width' => null, 'height' => null],
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
        'radar',
    ];

    public function __construct()
    {
        $num = count(static::$charts);
        $this->name("chart$num");
    }

    /**
     * @param $name
     *
     * @return $this|ChartJsComponentCore
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
     * @return ChartJsComponentCore
     */
    public function element($element)
    {
        return $this->set('element', $element);
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this|ChartJsComponentCore
     */
    private function set($key, $value)
    {
        Arr::set(static::$charts[$this->name], $key, $value);

        return $this;
    }

    /**
     * @param  array  $labels
     *
     * @return ChartJsComponentCore
     */
    public function labels(array $labels)
    {
        return $this->set('labels', $labels);
    }

    /**
     * @param  array  $datasets
     *
     * @return ChartJsComponentCore
     */
    public function datasets(array $datasets)
    {
        return $this->set('datasets', $datasets);
    }

    /**
     * @param  array  $datasets
     *
     * @return ChartJsComponentCore
     */
    public function simpleDatasets(string $label, array $dataset)
    {
        static::$charts[$this->name]['datasets'][] = [
            'label' => $label,
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
     * @return ChartJsComponentCore
     */
    public function type($type)
    {
        if (!in_array($type, $this->types)) {
            throw new InvalidArgumentException('Invalid Chart type.');
        }

        return $this->set('type', $type);
    }

    /**
     * @param  array  $size
     *
     * @return ChartJsComponentCore
     */
    public function size($size)
    {
        return $this->set('size', $size);
    }

    /**
     * @param  array  $options
     *
     * @return $this|ChartJsComponentCore
     */
    public function options(array $options)
    {
        foreach ($options as $key => $value) {
            $this->set('options.'.$key, $value);
        }

        return $this;
    }

    /**
     * @param  string|array  $optionsRaw
     * @return static
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

        return view('admin::segment.chartjs')
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
}
