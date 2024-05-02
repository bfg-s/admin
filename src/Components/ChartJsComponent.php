<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Components\Builders\ChartJsComponentBuilder;
use Admin\Delegates\SearchForm;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class ChartJsComponent extends Component
{
    /**
     * @var array
     */
    public static array $loadCallBacks = [];

    /**
     * @var int
     */
    protected static int $count = 0;

    /**
     * @var ChartJsComponentBuilder
     */
    public ChartJsComponentBuilder $builder;

    /**
     * @var mixed
     */
    protected mixed $dataBuilder = null;

    /**
     * @var int
     */
    protected int $size = 100;

    /**
     * @var string
     */
    protected string $type = 'line';

    /**
     * @var array
     */
    protected array $datasets = [];

    /**
     * @var string
     */
    protected string $view = 'chartjs';

    /**
     * @var SearchFormComponent|null
     */
    protected SearchFormComponent|null $searchForm = null;

    /**
     * @param  array  $delegates
     * @throws Throwable
     */
    public function __construct(...$delegates)
    {
        static::$count++;

        parent::__construct(...$delegates);

        $this->builder = new ChartJsComponentBuilder();

        $this->setDatas(['load' => 'chart::js']);
    }

    /**
     * @param  callable  $cb
     * @return $this
     */
    public function load(callable $cb): static
    {
        static::$loadCallBacks[$this->builder->getName()] = [$cb, $this, $this->model];

        return $this;
    }

    /**
     * @return SearchFormComponent|null
     */
    public function getSearchForm(): ?SearchFormComponent
    {
        return $this->searchForm;
    }

    /**
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
     * @return $this
     */
    public function typeBar(): static
    {
        return $this->type('bar');
    }

    /**
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
     * @return $this
     */
    public function typeHorizontalBar(): static
    {
        return $this->type('horizontalBar');
    }

    /**
     * @return $this
     */
    public function typeBubble(): static
    {
        return $this->type('bubble');
    }

    /**
     * @return $this
     */
    public function typeScatter(): static
    {
        return $this->type('scatter');
    }

    /**
     * @return $this
     */
    public function typeDoughnut(): static
    {
        return $this->type('doughnut');
    }

    /**
     * @return $this
     */
    public function typeLine(): static
    {
        return $this->type('line');
    }

    /**
     * @return $this
     */
    public function typePie(): static
    {
        return $this->type('pie');
    }

    /**
     * @return $this
     */
    public function typePolarArea(): static
    {
        return $this->type('polarArea');
    }

    /**
     * @return $this
     */
    public function typeRadar(): static
    {
        return $this->type('radar');
    }

    /**
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
     * @return $this
     */
    public function miniChart(): static
    {
        $this->builder
            ->type($this->type)
            //->size(['width' => 400, 'height' => $this->size])
            ->labels(collect($this->dataBuilder)->keys()->toArray());

        foreach ($this->datasets as $dataset) {
            $bgColor = $this->randColor();
            $this->builder->addDataset(
                [
                    'label' => __($dataset['title']),
                    'backgroundColor' => $this->renderColor($bgColor, '0.31'), //"rgba(38, 185, 154, 0.31)",
                    'borderColor' => $this->renderColor($bgColor, '0.7'), //"rgba(38, 185, 154, 0.7)",
                    'pointBorderColor' => $this->renderColor($bgColor, '0.7'), //"rgba(38, 185, 154, 0.7)",
                    'pointBackgroundColor' => $this->renderColor($bgColor, '0.7'), //"rgba(38, 185, 154, 0.7)",
                    'pointHoverBackgroundColor' => $this->randColor(), //"#fff",
                    'pointHoverBorderColor' => $this->randColor(),
                    'data' => collect($dataset['data'])->values()->toArray(),
                ]
            );
        }

        return $this;
    }

    /**
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
     * @param $c
     * @param  string  $opacity
     * @return string
     */
    public function renderColor($c, string $opacity = '1.0'): string
    {
        return "rgba({$c[0]}, {$c[1]}, {$c[2]}, $opacity)";
    }

    /**
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
            if ($isColorList) {
                $bgColor = $color[$key] ?? $this->randColor();
            } else {
                $bgColor = $color ?: $this->randColor();
            }

            $this->builder->addDataset(
                [
                    'label' => is_array($title) ? __($title[$key] ?? '') : __($title),
                    'backgroundColor' => array_is_list($color) && isset($color[0]) && is_array($color[0])
                        ? array_map(fn(array $c) => $this->renderColor($c, '0.31'), $color)
                        : $this->renderColor($bgColor, '0.31'),
                    'borderColor' => $this->renderColor($bgColor, '0.7'),
                    'pointBorderColor' => $this->renderColor($bgColor, '0.7'),
                    'pointBackgroundColor' => $this->renderColor($bgColor, '0.7'),
                    'pointHoverBackgroundColor' => $this->renderColor($this->randColor()),
                    'pointHoverBorderColor' => $this->renderColor($this->randColor()),
                    'data' => collect($datum)->values()->toArray(),
                ]
            );
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getViewData(): array
    {
        return $this->viewData();
    }

    /**
     * @return array
     */
    protected function viewData(): array
    {
        return array_merge($this->builder->getParams(), [
            'loading' => isset(static::$loadCallBacks[$this->builder->getName()])
        ]);
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
    }
}
