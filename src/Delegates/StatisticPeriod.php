<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\StatisticPeriodComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin StatisticPeriodComponent
 */
class StatisticPeriod extends Delegator
{
    protected $class = StatisticPeriodComponent::class;
}
