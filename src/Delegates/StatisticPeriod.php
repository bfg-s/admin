<?php

namespace Admin\Delegates;

use Admin\Components\StatisticPeriodComponent;
use Admin\Core\Delegator;

/**
 * @mixin StatisticPeriodComponent
 */
class StatisticPeriod extends Delegator
{
    protected $class = StatisticPeriodComponent::class;
}
