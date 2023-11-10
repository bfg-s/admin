<?php

namespace Admin\Components\Builders;

use Illuminate\Support\Arr;
use InvalidArgumentException;

class ChartJsComponentBuilder
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
     * @return $this|ChartJsComponentBuilder
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
     * @return ChartJsComponentBuilder
     */
    public function element($element)
    {
        return $this->set('element', $element);
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this|ChartJsComponentBuilder
     */
    private function set($key, $value)
    {
        Arr::set(static::$charts[$this->name], $key, $value);

        return $this;
    }

    /**
     * @param  array  $labels
     *
     * @return ChartJsComponentBuilder
     */
    public function labels(array $labels)
    {
        return $this->set('labels', $labels);
    }

    /**
     * @param  array  $datasets
     *
     * @return ChartJsComponentBuilder
     */
    public function datasets(array $datasets)
    {
        return $this->set('datasets', $datasets);
    }

    /**
     * @param  string  $label
     * @param  array  $dataset
     * @return ChartJsComponentBuilder
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
     * @return ChartJsComponentBuilder
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
     * @return ChartJsComponentBuilder
     */
    public function size($size)
    {
        return $this->set('size', $size);
    }

    /**
     * @param  array  $options
     *
     * @return $this|ChartJsComponentBuilder
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

    public function getParams()
    {
        $chart = static::$charts[$this->name];

        return [
            'isNotAjax' => !request()->ajax() && !request()->pjax(),
            'datasets' => $chart['datasets'],
            'element' => $this->name,
            'labels' => $chart['labels'],
            'options' => $chart['options'] ?? '',
            'optionsRaw' => $chart['optionsRaw'] ?? '',
            'type' => $chart['type'],
            'size' => $chart['size'],
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
