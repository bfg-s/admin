<?php

declare(strict_types=1);

namespace Admin\Widgets;

use Admin\Components\CardComponent;
use Admin\Components\WidgetComponent;
use Admin\Delegates\Card;
use Admin\Delegates\CardBody;
use Admin\Delegates\ChartJs;
use Admin\Delegates\SearchForm;

class ChartStatisticWidget extends WidgetAbstract
{
    /**
     * The name of the widget.
     *
     * @var string|null
     */
    protected string|null $name = 'Chart Statistic Widget';

    /**
     * The description of the widget.
     *
     * @var string|null
     */
    protected string|null $description = 'This widget displays a chart statistic.';

    /**
     * The icon of the widget.
     *
     * @var string|null
     */
    protected string|null $icon = 'fas fa-chart-area';

    /**
     * Settings for the widget.
     *
     * @var array
     */
    protected array $settings = [
        'model' => null,
        'title' => null,
        'label' => null,
        'size' => 100,
    ];

    /**
     * Settings description for the widget.
     *
     * @var array|string[]
     */
    protected array $settingsDescription = [
        'model' => 'The model to use for the statistic.',
        'title' => 'The title of the widget.',
        'label' => 'The label of the chart.',
        'size' => 'The size of the chart.',
    ];

    /**
     * Settings type for the widget.
     *
     * @var array
     */
    protected array $settingsType = [
        'model' => 'model_select',
        'title' => 'string',
        'label' => 'string',
        'size' => 'string',
    ];

    /**
     * @param  \Admin\Components\WidgetComponent  $widgetComponent
     * @param  \Admin\Delegates\Card  $card
     * @param  \Admin\Delegates\ChartJs  $chartJs
     * @param  \Admin\Delegates\CardBody  $cardBody
     * @param  \Admin\Delegates\SearchForm  $searchForm
     * @return \Admin\Components\WidgetComponent|\Admin\Components\CardComponent|null
     */
    public function handle(WidgetComponent $widgetComponent, Card $card, ChartJs $chartJs, CardBody $cardBody, SearchForm $searchForm): WidgetComponent|CardComponent|null
    {
        $model = $this->settings['model'];
        if ($model) {
            $title = $this->settings['title'] ?: '';
            $label = $this->settings['label'] ?: '';
            $size = $this->settings['size'] ?: 100;

            return $widgetComponent->card(
                $card->title(__($title))->card_body(
                    $cardBody->chart_js(
                        $chartJs->model($model)
                            ->hasSearch(
                                $searchForm->date_range('created_at', 'admin.created_at')
                                    ->default(implode(' - ', $this->defaultDateRange()))
                            )
                            ->size((int) $size)
                            ->loadModelBy(title: __($label)),
                    )
                )
            );
        }
        return null;
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
}
