<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\ChartJsComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin ChartJsComponent
 */
class ChartJs extends Delegator
{
    protected $class = ChartJsComponent::class;
}
