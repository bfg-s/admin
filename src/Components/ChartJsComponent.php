<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Components\Builders\ChartJsComponentBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

/**
 * The component that is responsible for displaying graphs on the admin panel pages.
 */
class ChartJsComponent extends Component
{
    /**
     * Storage of graph callbacks for lazy loading.
     *
     * @var array
     */
    public static array $loadCallBacks = [];

    /**
     * Counter of charts per page for a unique identifier.
     *
     * @var int
     */
    protected static int $count = 0;

    /**
     * A special builder class for Chart.js charts.
     *
     * @var ChartJsComponentBuilder
     */
    public ChartJsComponentBuilder $builder;

    /**
     * Display data for the graph builder.
     *
     * @var mixed
     */
    protected mixed $dataBuilder = null;

    /**
     * Set a custom size for the graph.
     *
     * @var int
     */
    protected int $size = 100;

    /**
     * Set the type for the graph.
     *
     * @var string
     */
    protected string $type = 'line';

    /**
     * Set of graph lines.
     *
     * @var array
     */
    protected array $datasets = [];

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'chartjs';

    /**
     * Link to the search form if available on the chart.
     *
     * @var SearchFormComponent|null
     */
    protected SearchFormComponent|null $searchForm = null;

    /**
     * ChartJsComponent constructor.
     *
     * @param  array  $delegates
     * @throws Throwable
     */
    public function __construct(...$delegates)
    {
        static::$count++;

        parent::__construct(...$delegates);

        $this->builder = new ChartJsComponentBuilder();
    }

    /**
     * Add a callback for lazy loading of the chart.
     *
     * @param  callable  $cb
     * @return $this
     */
    public function load(callable $cb): static
    {
        static::$loadCallBacks[$this->builder->getName()] = [$cb, $this, $this->model];

        return $this;
    }

    /**
     * Get a schedule search form if available.
     *
     * @return SearchFormComponent|null
     */
    public function getSearchForm(): SearchFormComponent|null
    {
        return $this->searchForm;
    }

    /**
     * An add-on for lazy loading, it allows you to extract data from the database and group it by a specified field.
     *
     * @param  string  $by
     * @param  array|string|null  $title
     * @return $this
     */
    public function loadModelBy(
        string $by = "created_at",
        array|string|null $title = null,
    ): static {
        $isDate = str_ends_with($by, '_at');
        $this->load(function (ChartJsComponent $chartJs) use ($by, $isDate, $title) {

            $query = $chartJs->realModel()->getQuery();

            if ($this->searchForm) {

                $query = $this->searchForm->makeModel($query);
            }

            $result = $query->select([
                $isDate ? DB::raw("DATE({$by}) as date") : $by,
                DB::raw('COUNT(*) as count')
            ])->groupBy('date')->get()->mapWithKeys(function ($item) use ($by, $isDate) {
                $item = (array) $item;
                return [$item[$isDate ? 'date' : $by] => $item['count']];
            });

            $chartJs->customChart(
                $title ?: Str::plural(class_basename(get_class($chartJs->realModel()))),
                $result
            );
        });

        return $this;
    }

    /**
     * A method that adds and describes a search form for a graph.
     *
     * @param ...$delegates
     * @return $this
     */
    public function hasSearch(...$delegates): static
    {
        $this->searchForm = $this->div()->search_form($delegates);

        $this->model = $this->searchForm->makeModel($this->model);

        return $this;
    }

    /**
     * Set a custom chart size.
     *
     * @param  int  $height
     * @param  int  $width
     * @return $this
     */
    public function size(int $height, int $width = 400): static
    {
        $this->builder->size(['width' => $width, 'height' => $height]);

        return $this;
    }

    /**
     * Set a custom chart type.
     *
     * @param  string  $type
     * @return $this
     */
    public function type(string $type): static
    {
        $this->type = $type;
        $this->builder->type($type);

        return $this;
    }

    /**
     * Set a custom "bar" type for the chart.
     *
     * @return $this
     */
    public function typeBar(): static
    {
        return $this->type('bar');
    }

    /**
     * Set a custom "horizontalBar" type for the chart.
     *
     * @return $this
     */
    public function typeHorizontalBar(): static
    {
        return $this->type('horizontalBar');
    }

    /**
     * Set a custom "bubble" type for the chart.
     *
     * @return $this
     */
    public function typeBubble(): static
    {
        return $this->type('bubble');
    }

    /**
     * Set a custom "scatter" type for the chart.
     *
     * @return $this
     */
    public function typeScatter(): static
    {
        return $this->type('scatter');
    }

    /**
     * Set a custom "doughnut" type for the graph.
     *
     * @return $this
     */
    public function typeDoughnut(): static
    {
        return $this->type('doughnut');
    }

    /**
     * Set a custom "line" type for the graph.
     *
     * @return $this
     */
    public function typeLine(): static
    {
        return $this->type('line');
    }

    /**
     * Set a custom "pie" type for the chart.
     *
     * @return $this
     */
    public function typePie(): static
    {
        return $this->type('pie');
    }

    /**
     * Set a custom type "polarArea" for the graph.
     *
     * @return $this
     */
    public function typePolarArea(): static
    {
        return $this->type('polarArea');
    }

    /**
     * Set a custom type "radar" for the chart.
     *
     * @return $this
     */
    public function typeRadar(): static
    {
        return $this->type('radar');
    }

    /**
     * Set the default data to be obtained between two data.
     *
     * @param  string  $column
     * @param $from
     * @param $to
     * @return ChartJsComponent|$this
     */
    public function setDefaultDataBetween(string $column, $from, $to): ChartJsComponent|static
    {
        return !request()->has('q') ? $this->setDataBetween($column, $from, $to) : $this;
    }

    /**
     * Set the data to be obtained between two data.
     *
     * @param  string  $column
     * @param $from
     * @param $to
     * @return $this
     */
    public function setDataBetween(string $column, $from, $to): static
    {
        return $this->prepareData(static function ($model) use ($column, $from, $to) {
            return $model->whereBetween($column, [$from, $to]);
        });
    }

    /**
     * Prepare data for graphs.
     *
     * @param  callable|null  $callback
     * @return $this
     */
    public function prepareData(callable $callback = null): static
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

    /**
     * Group data by column for graphs.
     *
     * @param  string  $atColumn
     * @param  string  $format
     * @return $this
     */
    public function groupDataByAt(string $atColumn, string $format = 'Y.m.d'): static
    {
        return $this->prepareData(static function ($model) use ($atColumn, $format) {
            return ($model instanceof Collection ? $model : $model->get())
                ->sortBy($atColumn)->groupBy(static function (Model $model) use ($atColumn, $format) {
                    return ($model->{$atColumn} instanceof Carbon ? $model->{$atColumn} : Carbon::parse($model->{$atColumn}))->format($format);
                });
        });
    }

    /**
     * Group all prepared data for graphs.
     *
     * @param  string  $column
     * @return $this
     */
    public function groupDataBy(string $column): static
    {
        return $this->prepareData(static function ($model) use ($column) {
            return $model->get()->groupBy(static function (Model $model) use ($column) {
                return $model->{$column};
            });
        });
    }

    /**
     * Count all prepared data points for graphs.
     *
     * @param  string  $title
     * @return $this
     */
    public function eachPointCount(string $title): static
    {
        return $this->eachPoint($title, static function (Collection $collection) {
            return $collection->count();
        });
    }

    /**
     * Process each point using a callback.
     *
     * @param  string  $title
     * @param  mixed|null  $callback
     * @param  mixed  $default
     * @return $this
     */
    public function eachPoint(string $title, mixed $callback = null, mixed $default = 0): static
    {
        $this->datasets[] = [
            'title' => $title,
            'data' => collect($this->dataBuilder)->map(static function ($collection) use ($callback, $default) {
                return $collection instanceof Collection ? call_user_func($callback, $collection) : $default;
            }),
        ];

        return $this;
    }

    /**
     * Count each point using a callback.
     *
     * @param  string  $title
     * @param $callback
     * @return $this
     */
    public function eachPointSum(string $title, $callback = null): static
    {
        return $this->eachPoint($title, static function (Collection $collection) use ($callback) {
            return $collection->sum($callback);
        });
    }

    /**
     * Convert already prepared data into a mini graph
     *
     * @return $this
     */
    public function miniChart(): static
    {
        $this->builder
            ->type($this->type)
            //->size(['width' => 400, 'height' => $this->size])
            ->labels(collect($this->dataBuilder)->keys()->toArray());

        foreach ($this->datasets as $dataset) {
            $label = __($dataset['title']);
            $bgColor = $this->randColorByName($label, 1);
            $this->builder->addDataset(
                [
                    'label' => $label,
                    'backgroundColor' => $this->renderColor($bgColor, '0.31'), //"rgba(38, 185, 154, 0.31)",
                    'borderColor' => $this->renderColor($bgColor, '0.7'), //"rgba(38, 185, 154, 0.7)",
                    'pointBorderColor' => $this->renderColor($bgColor, '0.7'), //"rgba(38, 185, 154, 0.7)",
                    'pointBackgroundColor' => $this->renderColor($bgColor, '0.7'), //"rgba(38, 185, 154, 0.7)",
                    'pointHoverBackgroundColor' => $this->randColorByName($label, 2), //"#fff",
                    'pointHoverBorderColor' => $this->randColorByName($label, 3),
                    'data' => collect($dataset['data'])->values()->toArray(),
                ]
            );
        }

        return $this;
    }

    /**
     * Transform a color from an array to an rgba string.
     *
     * @param $c
     * @param  string  $opacity
     * @return string
     */
    public function renderColor($c, string $opacity = '1.0'): string
    {
        return "rgba({$c[0]}, {$c[1]}, {$c[2]}, $opacity)";
    }

    /**
     * Create and get a random color for graph lines.
     *
     * @param  int  $min
     * @param  int  $max
     * @return array
     */
    public function randColor(int $min = 1, int $max = 255): array
    {
        $r1 = rand($min, $max);
        $r2 = rand($min, $max);
        $r3 = rand($min, $max);

        return [$r1, $r2, $r3];
    }

    /**
     * Create and get a random color for graph lines.
     *
     * @param  string  $title
     * @param  string|int|null  $salt
     * @return array
     */
    public function randColorByName(string $title, string|int $salt = null): array
    {
        if ($salt) {

            $title = $title . '-' . $salt;
        }
        $hashString = (string) crc32($title);
        $result = [];
        $length = strlen($hashString);

        for ($i = 0; $i < $length; $i += 3) {

            $result[] = substr($hashString, $i, 3);
        }

        $inst = array_map(fn ($number) => intval($number) % 256, $result);

        return [$inst[0], $inst[1], $inst[2]];
    }

    /**
     * Describe an arbitrary graph with arbitrary data.
     *
     * @param  string|array  $title
     * @param  Collection|array  $data
     * @param  array  $color
     * @return $this
     */
    public function customChart(string|array $title, Collection|array $data, array $color = []): static
    {
        if ($data instanceof Collection) {

            $data = $data->toArray();
        }

        $isDataList = array_is_list($data);
        $isColorList = $color && isset($color[0]) && is_array($color[0]);
        $data = !$isDataList || $data === [] ? [$data] : $data;

        $this->builder
            ->type($this->type)
            ->labels(collect($data[0])->keys()->toArray());

        foreach ($data as $key => $datum) {

            $label = is_array($title) ? __($title[$key] ?? '') : __($title);

            if ($isColorList) {
                $bgColor = $color[$key] ?? $this->randColorByName($label, 1);
            } else {
                $bgColor = $color ?: $this->randColorByName($label, 2);
            }

            $this->builder->addDataset(
                [
                    'label' => $label,
                    'backgroundColor' => array_is_list($color) && isset($color[0]) && is_array($color[0])
                        ? array_map(fn(array $c) => $this->renderColor($c, '0.31'), $color)
                        : $this->renderColor($bgColor, '0.31'),
                    'borderColor' => $this->renderColor($bgColor, '0.7'),
                    'pointBorderColor' => $this->renderColor($bgColor, '0.7'),
                    'pointBackgroundColor' => $this->renderColor($bgColor, '0.7'),
                    'pointHoverBackgroundColor' => $this->renderColor($this->randColorByName($label, 3)),
                    'pointHoverBorderColor' => $this->renderColor($this->randColorByName($label, 4)),
                    'data' => collect($datum)->values()->toArray(),
                ]
            );
        }

        return $this;
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return array
     */
    protected function viewData(): array
    {
        return array_merge($this->builder->getParams(), [
            'loading' => isset(static::$loadCallBacks[$this->builder->getName()])
        ]);
    }

    /**
     * Get additional data to be sent to the template.
     *
     * @return array
     */
    public function getViewData(): array
    {
        return $this->viewData();
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        $params = $this->viewData();

        $this->dataLoad('chart::js', [
            'type' => $params['type'],
            'labels' => $params['labels'],
            'datasets' => $params['datasets'],
            'options' => $params['optionsRaw'] ?: $params['options'],
            'name' => $params['element'],
            'loading' => $params['loading'],
            'loaderId' => $params['element'] . 'Loader',
            'timeout' => $this->realTimeTimeout,
        ]);
    }
}
