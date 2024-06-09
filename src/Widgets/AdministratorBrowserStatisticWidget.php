<?php

declare(strict_types=1);

namespace Admin\Widgets;

use Admin\Components\CardComponent;
use Admin\Components\WidgetComponent;
use Admin\Delegates\Card;
use Admin\Delegates\CardBody;
use Admin\Delegates\ChartJs;
use Admin\Models\AdminBrowser;
use Illuminate\Support\Collection;

class AdministratorBrowserStatisticWidget extends WidgetAbstract
{
    /**
     * The name of the widget.
     *
     * @var string|null
     */
    protected string|null $name = 'Administrator Browsers Statistic Widget';

    /**
     * The description of the widget.
     *
     * @var string|null
     */
    protected string|null $description = 'This widget displays a administrator browsers statistic.';

    /**
     * The icon of the widget.
     *
     * @var string|null
     */
    protected string|null $icon = 'fab fa-internet-explorer';

    /**
     * Settings for the widget.
     *
     * @var array
     */
    protected array $settings = [
        'size' => 200,
    ];

    /**
     * Settings description for the widget.
     *
     * @var array|string[]
     */
    protected array $settingsDescription = [
        'size' => 'The size of the chart.',
    ];

    /**
     * Settings type for the widget.
     *
     * @var array
     */
    protected array $settingsType = [
        'size' => 'string',
    ];

    /**
     * @param  \Admin\Components\WidgetComponent  $widgetComponent
     * @param  \Admin\Delegates\Card  $card
     * @param  \Admin\Delegates\ChartJs  $chartJs
     * @param  \Admin\Delegates\CardBody  $cardBody
     * @return \Admin\Components\WidgetComponent|\Admin\Components\CardComponent|null
     */
    public function handle(WidgetComponent $widgetComponent, Card $card, ChartJs $chartJs, CardBody $cardBody): WidgetComponent|CardComponent|null
    {
        $size = $this->settings['size'] ?: 100;

        return $widgetComponent->card(
            $card->title(__('admin.administrators_browser_statistic')),
            $card->card_body(
                $cardBody->chart_js(
                    $chartJs->size((int) $size)->typeDoughnut(),
                    $chartJs->load(function (\Admin\Components\ChartJsComponent $component) {
                        $adminLogs = AdminBrowser::all(['name'])->groupBy('name')->map(
                            fn(Collection $collection) => $collection->count()
                        );
                        $component->customChart(__('admin.browser'), [$adminLogs->toArray()], $adminLogs->map(
                            fn(int $count, string $name) => $component->randColorByName($name)
                        )->values()->toArray());
                    }),
                )
            ),
        );
    }
}
