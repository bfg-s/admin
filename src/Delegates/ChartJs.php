<?php

namespace Admin\Delegates;

use Admin\Components\ChartJsComponent;
use Admin\Core\Delegator;

/**
 * @mixin ChartJsComponent
 */
class ChartJs extends Delegator
{
    protected $class = ChartJsComponent::class;
}
