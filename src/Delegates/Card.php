<?php

namespace LteAdmin\Delegates;

use App\Admin\Delegates\ChartJs;
use App\Admin\Delegates\ModelTable;
use App\Admin\Delegates\StatisticPeriod;
use LteAdmin\Components\CardComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin CardComponent
 */
class Card extends Delegator
{
    protected $class = CardComponent::class;

    public function defaultDateRange(): array
    {
        return [
            now()->subYear()->toDateString(),
            now()->addDay()->toDateString(),
        ];
    }

    public function statisticBody(...$delegates): array
    {
        $statisticPeriod = new StatisticPeriod();
        $modelTable = new ModelTable();
        $chartJs = new ChartJs();

        return [
            $this->ifNotQuery('chart')->buttons()->warning(['fas fa-chart-line', 'Statistic'])
                ->switchQuery('chart'),
            $this->ifQuery('chart')->buttons()->secondary(['fas fa-table', __('lte.list')])
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
                ->chart_js(
                    $chartJs->setDefaultDataBetween('created_at', ...$this->defaultDateRange())
                        ->groupDataByAt('created_at')
                        ->eachPointCount('Created')
                        ->miniChart(),
                ),
        ];
    }

    public function sortedModelTable(...$delegators): array
    {
        $statisticPeriod = new StatisticPeriod();
        $modelTable = new ModelTable();
        $chartJs = new ChartJs();

        return [
            $this->ifNotQuery('screen', 1)->buttons()->info(['fas fa-stream', __('lte.sort')])
                ->setQuery('screen', 1),

            $this->ifNotQuery('screen', 2)->buttons()->warning(['fas fa-chart-line', 'Statistic'])
                ->setQuery('screen', 2),

            $this->ifQuery('screen')->buttons()->secondary(['fas fa-table', __('lte.list')])
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
                ->chart_js(
                    $chartJs->setDefaultDataBetween('created_at', ...$this->defaultDateRange())
                        ->groupDataByAt('created_at')
                        ->eachPointCount('Created')
                        ->miniChart(),
                ),
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
}
