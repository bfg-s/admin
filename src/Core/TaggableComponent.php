<?php

namespace Lar\LteAdmin\Core;

use Lar\Layout\Abstracts\Component;
use Lar\LteAdmin\Components\AlertComponent;
use Lar\LteAdmin\Components\FormFooterComponent;
use Lar\LteAdmin\Components\StatisticPeriodComponent;
use Lar\LteAdmin\Components\TableComponent;
use Lar\LteAdmin\Components\TimelineComponent;
use Lar\LteAdmin\Components\DividerComponent;
use Lar\LteAdmin\Components\FieldComponent;
use Lar\LteAdmin\Components\InfoBoxComponent;
use Lar\LteAdmin\Components\LangComponent;
use Lar\LteAdmin\Components\ModelRelationComponent;
use Lar\LteAdmin\Components\SmallBoxComponent;
use Lar\LteAdmin\Components\TabsComponent;
use Lar\LteAdmin\Controllers\Controller;

class TaggableComponent extends Component
{
    /**
     * @var string[]
     */
    protected static $collection = [
        'lang' => LangComponent::class,
        'form_footer' => FormFooterComponent::class,
        'field' => FieldComponent::class,
        'table' => TableComponent::class,
        'alert' => AlertComponent::class,
        'small_box' => SmallBoxComponent::class,
        'info_box' => InfoBoxComponent::class,
        'tabs' => TabsComponent::class,
        'divider' => DividerComponent::class,
        'model_relation' => ModelRelationComponent::class,
        'timeline' => TimelineComponent::class,
        'statistic_periods' => StatisticPeriodComponent::class,
    ];

    /**
     * TagableComponent constructor.
     */
    public function __construct()
    {
        static::injectCollection(static::$collection);
        static::injectCollection(Controller::getExplanationList());
    }
}
