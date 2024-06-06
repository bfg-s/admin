<?php

declare(strict_types=1);

namespace Admin\Widgets;

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
     * The slug of the widget.
     *
     * @var string
     */
    protected string $slug = 'period-statistic-widget';

    /**
     * Settings for the widget.
     *
     * @var array
     */
    protected array $settings = [
        'model' => null,
        'title' => null,
        'icon' => null,
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
     * @param  \Admin\Delegates\StatisticPeriod  $statisticPeriod
     * @return \Admin\Delegates\StatisticPeriod|array
     */
    protected function handle(StatisticPeriod $statisticPeriod): StatisticPeriod|array
    {
        $model = $this->settings['model'];
        if ($model) {
            $title = $this->settings['title'];
            $icon = $this->settings['icon'];
            $forToday = $this->settings['forToday'];
            $perWeek = $this->settings['perWeek'];
            $perYear = $this->settings['perYear'];
            $total = $this->settings['total'];

            $statisticPeriod->model($model);
            if ($title) {
                $statisticPeriod->title($title);
            }
            if ($icon) {
                $statisticPeriod->icon($icon);
            }
            if ($forToday) {
                $statisticPeriod->forToday();
            }
            if ($perWeek) {
                $statisticPeriod->perWeek();
            }
            if ($perYear) {
                $statisticPeriod->perYear();
            }
            if ($total) {
                $statisticPeriod->total();
            }
            return $statisticPeriod;
        }
        return [];
    }
}
