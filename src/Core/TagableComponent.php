<?php

namespace Lar\LteAdmin\Core;

use Lar\Layout\Abstracts\Component;
use Lar\LteAdmin\Segments\Tagable\Alert;
use Lar\LteAdmin\Segments\Tagable\ButtonGroup;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\ChartJs;
use Lar\LteAdmin\Segments\Tagable\Col;
use Lar\LteAdmin\Segments\Tagable\Divider;
use Lar\LteAdmin\Segments\Tagable\Field;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\FormFooter;
use Lar\LteAdmin\Segments\Tagable\InfoBox;
use Lar\LteAdmin\Segments\Tagable\Lang;
use Lar\LteAdmin\Segments\Tagable\Live;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;
use Lar\LteAdmin\Segments\Tagable\ModelLive;
use Lar\LteAdmin\Segments\Tagable\ModelRelation;
use Lar\LteAdmin\Segments\Tagable\ModelTable;
use Lar\LteAdmin\Segments\Tagable\Nested;
use Lar\LteAdmin\Segments\Tagable\Row;
use Lar\LteAdmin\Segments\Tagable\SmallBox;
use Lar\LteAdmin\Segments\Tagable\StatisticPeriods;
use Lar\LteAdmin\Segments\Tagable\Table;
use Lar\LteAdmin\Segments\Tagable\Tabs;
use Lar\LteAdmin\Segments\Tagable\Timeline;

/**
 * Class TagableComponent
 * @package Lar\LteAdmin\Core
 */
class TagableComponent extends Component {

    /**
     * @var string[]
     */
    protected $collection = [
        'row' => Row::class,
        'col' => Col::class,
        'lang' => Lang::class,
        'card' => Card::class,
        'form' => Form::class,
        'form_footer' => FormFooter::class,
        'field' => Field::class,
        'model_table' => ModelTable::class,
        'model_info_table' => ModelInfoTable::class,
        'table' => Table::class,
        'button_group' => ButtonGroup::class,
        'alert' => Alert::class,
        'small_box' => SmallBox::class,
        'info_box' => InfoBox::class,
        'tabs' => Tabs::class,
        'nested' => Nested::class,
        'divider' => Divider::class,
        'live' => Live::class,
        'model_live' => ModelLive::class,
        'model_relation' => ModelRelation::class,
        'timeline' => Timeline::class,
        'statistic_periods' => StatisticPeriods::class,
        'chart_js' => ChartJs::class,
    ];

    /**
     * TagableComponent constructor.
     */
    public function __construct()
    {
        static::injectCollection($this->collection);
    }
}
