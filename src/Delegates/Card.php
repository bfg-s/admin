<?php

declare(strict_types=1);

namespace Admin\Delegates;

use Admin\Components\CardComponent;
use Admin\Components\ChartJsComponent;
use Admin\Core\Delegator;
use Closure;
use Illuminate\Support\Traits\Macroable;

/**
 * @mixin CardComponent
 * @mixin MacroMethodsForCard
 */
class Card extends Delegator
{
    use Macroable;

    protected $class = CardComponent::class;

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

    public function statisticBody(...$delegates): array
    {
        $statisticPeriod = new StatisticPeriod();
        $modelTable = new ModelTable();
        $searchForm = new SearchForm();

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
                $statisticPeriod->m3()
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
                ->loadModelBy(title: __('admin.created'))
        ];
    }

    public function defaultDateRange(): array
    {
        return [
            now()->subYear()->toDateString(),
            now()->addDay()->toDateString(),
        ];
    }

    public function nestedModelTable(...$delegators): array
    {
        return [
            $this->ifQuery('sort')->nestedTools(),
            $this->ifQuery('screen', 1)->nestedTools(),
            $this->sortedModelTable(...$delegators),
        ];
    }

    public function sortedModelTable(...$delegators): array
    {
        $statisticPeriod = new StatisticPeriod();
        $modelTable = new ModelTable();

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
                    $statisticPeriod->m3()
                        ->icon_gift()
                        ->forToday()
                        ->perWeek()
                        ->perYear()
                        ->total()
                ),
            $this->ifQuery('screen', 2)
                ->chart_js()
                ->loadModelBy(title: __('admin.created')),
        ];
    }
}
