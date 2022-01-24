<?php

namespace Lar\LteAdmin\Delegates;

use Lar\LteAdmin\Components\StatisticPeriodComponent;
use Lar\LteAdmin\Core\Delegator;

/**
 * @mixin StatisticPeriodComponent
 */
class StatisticPeriod extends Delegator
{
    protected $class = StatisticPeriodComponent::class;
}
