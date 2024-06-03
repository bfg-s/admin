<?php

declare(strict_types=1);

namespace Admin\Components\Builders;

use Illuminate\Support\Arr;
use InvalidArgumentException;

/**
 * Special kernel chart builder ChartJS.
 */
class ChartJsComponentBuilder
{
    /**
     * List of chart builders.
     *
     * @var array
     */
    protected static array $charts = [];

    /**
     * Unique name of the current chart.
     *
     * @var string|null
     */
    private string|null $name = null;

    /**
     * Default settings for the current chart.
     *
     * @var array
     */
    private array $defaults = [
        'datasets' => [],
        'labels' => [],
        'type' => 'line',
        'options' => [],
        'size' => ['width' => null, 'height' => null],
    ];

    /**
     * List of acceptable chart types.
     *
     * @var array
     */
    private array $types = [
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

    /**
     * ChartJsComponentBuilder constructor.
     */
    public function __construct()
    {
        $num = count(static::$charts);
        $this->name("chart$num");
    }

    /**
     * Set a unique name for the chart.
     *
     * @param $name
     * @return $this
     */
    public function name($name): static
    {
        $old = static::$charts[$this->name] ?? [];
        $this->name = $name;
        static::$charts[$name] = array_merge($this->defaults, $old);

        return $this;
    }

    /**
     * Set graphic element.
     *
     * @param $element
     * @return $this
     */
    public function element($element): static
    {
        return $this->set('element', $element);
    }

    /**
     * Set the value of the current chart.
     *
     * @param $key
     * @param $value
     * @return $this
     */
    private function set($key, $value): static
    {
        Arr::set(static::$charts[$this->name], $key, $value);

        return $this;
    }

    /**
     * Set labels for the current chart.
     *
     * @param  array  $labels
     * @return $this
     */
    public function labels(array $labels): static
    {
        return $this->set('labels', $labels);
    }

    /**
     * Install datasets of the current chart.
     *
     * @param  array  $datasets
     * @return $this
     */
    public function datasets(array $datasets): static
    {
        return $this->set('datasets', $datasets);
    }

    /**
     * Install simple datasets of the current chart.
     *
     * @param  string  $label
     * @param  array  $dataset
     * @return $this
     */
    public function simpleDatasets(string $label, array $dataset): static
    {
        static::$charts[$this->name]['datasets'][] = [
            'label' => $label,
            'data' => $dataset,
        ];

        return $this;
    }

    /**
     * Add datasets of the current chart.
     *
     * @param  array  $dataset
     * @return $this
     */
    public function addDataset(array $dataset): static
    {
        static::$charts[$this->name]['datasets'][] = $dataset;

        return $this;
    }

    /**
     * Set the current chart type.
     *
     * @param $type
     * @return $this
     */
    public function type($type): static
    {
        if (!in_array($type, $this->types)) {
            throw new InvalidArgumentException('Invalid Chart type.');
        }

        return $this->set('type', $type);
    }

    /**
     * Set the size of the current chart.
     *
     * @param  array  $size
     * @return $this
     */
    public function size(array $size): static
    {
        return $this->set('size', $size);
    }

    /**
     * Set options for the current chart.
     *
     * @param  array  $options
     * @return $this
     */
    public function options(array $options): static
    {
        foreach ($options as $key => $value) {
            $this->set('options.'.$key, $value);
        }

        return $this;
    }

    /**
     * Set raw options for the current chart.
     *
     * @param  array|string  $optionsRaw
     * @return static
     */
    public function optionsRaw(array|string $optionsRaw): static
    {
        if (is_array($optionsRaw)) {

            $this->set('optionsRaw', json_encode($optionsRaw));

            return $this;
        }

        $this->set('optionsRaw', $optionsRaw);

        return $this;
    }

    /**
     * Get all parameters of the current chart.
     *
     * @return array
     */
    public function getParams(): array
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
     * Get the name of the current chart.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
