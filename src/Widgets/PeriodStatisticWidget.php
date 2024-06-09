<?php

declare(strict_types=1);

namespace Admin\Widgets;

use Admin\Components\StatisticPeriodComponent;
use Admin\Components\WidgetComponent;
use Admin\Delegates\StatisticPeriod;

class PeriodStatisticWidget extends WidgetAbstract
{
    /**
     * The name of the widget.
     *
     * @var string|null
     */
    protected string|null $name = 'Period Statistic Widget';

    /**
     * The description of the widget.
     *
     * @var string|null
     */
    protected string|null $description = 'This widget displays a period statistic.';

    /**
     * The icon of the widget.
     *
     * @var string|null
     */
    protected string|null $icon = 'fas fa-chart-pie';

    /**
     * Settings for the widget.
     *
     * @var array
     */
    protected array $settings = [
        'model' => null,
        'title' => null,
        'icon' => 'fas fa-chart-pie',
        'forToday' => true,
        'perWeek' => true,
        'perYear' => true,
        'total' => true,
    ];

    /**
     * Settings description for the widget.
     *
     * @var array|string[]
     */
    protected array $settingsDescription = [
        'model' => 'The model to use for the statistic.',
        'title' => 'The title of the widget.',
        'icon' => 'The icon of the widget.',
        'forToday' => 'Whether to display the statistic for today.',
        'perWeek' => 'Whether to display the statistic per week.',
        'perYear' => 'Whether to display the statistic per year.',
        'total' => 'Whether to display the total statistic.',
    ];

    /**
     * Settings type for the widget.
     *
     * @var array
     */
    protected array $settingsType = [
        'model' => 'model_select',
        'title' => 'string',
        'icon' => 'string',
        'forToday' => 'boolean',
        'perWeek' => 'boolean',
        'perYear' => 'boolean',
        'total' => 'boolean',
    ];

    /**
     * @param  \Admin\Components\WidgetComponent  $widgetComponent
     * @param  \Admin\Delegates\StatisticPeriod  $statisticPeriod
     * @return \Admin\Components\StatisticPeriodComponent|\Admin\Core\Delegate|array
     */
    public function handle(WidgetComponent $widgetComponent, StatisticPeriod $statisticPeriod): StatisticPeriodComponent|WidgetComponent|null
    {
        $model = $this->settings['model'];
        if ($model) {
            $title = $this->settings['title'];
            $icon = $this->settings['icon'];
            $forToday = $this->settings['forToday'];
            $perWeek = $this->settings['perWeek'];
            $perYear = $this->settings['perYear'];
            $total = $this->settings['total'];

            return $widgetComponent->statistic_period(
                $statisticPeriod->model($model)
                    ->when($title, fn ($statisticPeriod) => $statisticPeriod->title($title))
                    ->when($icon, fn ($statisticPeriod) => $statisticPeriod->icon($icon))
                    ->when($forToday, fn ($statisticPeriod) => $statisticPeriod->forToday())
                    ->when($perWeek, fn ($statisticPeriod) => $statisticPeriod->perWeek())
                    ->when($perYear, fn ($statisticPeriod) => $statisticPeriod->perYear())
                    ->when($total, fn ($statisticPeriod) => $statisticPeriod->total())
            );
        }
        return null;
    }
}
