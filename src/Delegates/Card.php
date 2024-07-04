<?php

declare(strict_types=1);

namespace Admin\Delegates;

use Admin\Components\CardComponent;
use Admin\Components\ChartJsComponent;
use Admin\Core\Delegator;
use Closure;
use Illuminate\Support\Traits\Macroable;

/**
 * The delegation that is responsible for the card component.
 *
 * @mixin CardComponent
 * @mixin MacroMethodsForCard
 */
class Card extends Delegator
{
    use Macroable;

    /**
     * Delegated actions for class.
     *
     * @var string
     */
    protected $class = CardComponent::class;

    /**
     * Period statistics component.
     *
     * @var StatisticPeriod|string
     */
    protected StatisticPeriod|string $statisticPeriodClass = StatisticPeriod::class;

    /**
     * Model table component.
     *
     * @var ModelTable|string
     */
    protected ModelTable|string $modelTableClass = ModelTable::class;

    /**
     * Search form component.
     *
     * @var SearchForm|string
     */
    protected SearchForm|string $searchFormClass = SearchForm::class;

    /**
     * Magic method for macros.
     *
     * @param $method
     * @param $parameters
     * @return \Admin\Core\Delegate|mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            $macro = static::$macros[$method];

            if ($macro instanceof Closure) {
                $macro = $macro->bindTo($this, static::class);
            }

            return $macro(...$parameters);
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Use the period statistics component.
     *
     * @param  StatisticPeriod|string  $statisticPeriod
     * @return $this
     */
    public function withStatisticPeriodClass(StatisticPeriod|string $statisticPeriod): static
    {
        $this->statisticPeriodClass = $statisticPeriod;

        return $this;
    }

    /**
     * Use the model table component.
     *
     * @param  ModelTable|string  $modelTable
     * @return $this
     */
    public function withModelTableClass(ModelTable|string $modelTable): static
    {
        $this->modelTableClass = $modelTable;

        return $this;
    }

    /**
     * Use the search form component.
     *
     * @param  SearchForm|string  $searchForm
     * @return $this
     */
    public function withSearchFormClass(SearchForm|string $searchForm): static
    {
        $this->searchFormClass = $searchForm;

        return $this;
    }

    /**
     * Make a statistician period body.
     *
     * @param ...$delegates
     * @return array
     */
    public function statisticBody(...$delegates): array
    {
        $statisticPeriod = is_string($this->statisticPeriodClass) ? new $this->statisticPeriodClass() : $this->statisticPeriodClass;
        $modelTable = is_string($this->modelTableClass) ? new $this->modelTableClass() : $this->modelTableClass;
        $searchForm = is_string($this->searchFormClass) ? new $this->searchFormClass() : $this->searchFormClass;

        return [
            $this->ifNotQuery('chart')->buttons()->warning(['fas fa-chart-line', __('admin.statistic')])
                ->switchQuery('chart'),
            $this->ifQuery('chart')->buttons()->secondary(['fas fa-table', __('admin.list')])
                ->switchQuery('chart'),
            $this->ifNotQuery('chart')->model_table(
                $modelTable->colDefault(
                    ...$delegates
                ),
            ),
            $this->ifQuery('chart')->statistic_period(
                $statisticPeriod
                    ->icon_gift()
                    ->forToday()
                    ->perWeek()
                    ->perYear()
                    ->total()
            ),
            $this->ifQuery('chart')
                ->chart_js()
                ->hasSearch(
                    $searchForm->date_range('created_at', 'admin.created_at')
                        ->default(implode(' - ', $this->defaultDateRange()))
                )
                ->size(100)
                ->loadModelBy(title: __('admin.created'))
        ];
    }

    /**
     * Default date interval.
     *
     * @return array
     */
    public function defaultDateRange(): array
    {
        return [
            now()->subYear()->toDateString(),
            now()->addDay()->toDateString(),
        ];
    }

    /**
     * Make a nested body.
     *
     * @param ...$delegators
     * @return array
     */
    public function nestedModelTable(...$delegators): array
    {
        return [
            $this->ifQuery('sort')->nestedTools(),
            $this->ifQuery('screen', 1)->nestedTools(),
            $this->sortedModelTable(...$delegators),
        ];
    }

    /**
     * Make a sortable body.
     *
     * @param ...$delegators
     * @return array
     */
    public function sortedModelTable(...$delegators): array
    {
        $statisticPeriod = is_string($this->statisticPeriodClass) ? new $this->statisticPeriodClass() : $this->statisticPeriodClass;
        $modelTable = is_string($this->modelTableClass) ? new $this->modelTableClass() : $this->modelTableClass;

        return [
            $this->ifNotQuery('screen', 1)->buttons()->info(['fas fa-stream', __('admin.sort')])
                ->setQuery('screen', 1),

            $this->ifNotQuery('screen', 2)->buttons()->warning(['fas fa-chart-line', __('admin.statistic')])
                ->setQuery('screen', 2),

            $this->ifQuery('screen')->buttons()->secondary(['fas fa-table', __('admin.list')])
                ->forgetQuery('screen'),

            $this->ifNotQuery('screen')->model_table(
                $modelTable->orderBy('order'),
                $modelTable->colDefault(...$delegators),
            ),
            $this->ifQuery('screen', 1)
                ->nested(...$delegators),
            $this->ifQuery('screen', 2)
                ->statistic_period(
                    $statisticPeriod
                        ->icon_gift()
                        ->forToday()
                        ->perWeek()
                        ->perYear()
                        ->total()
                ),
            $this->ifQuery('screen', 2)
                ->chart_js()
                ->size(100)
                ->loadModelBy(title: __('admin.created')),
        ];
    }
}
