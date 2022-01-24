<?php

namespace Lar\LteAdmin\Delegates;

use Lar\LteAdmin\Components\ChartJsComponent;
use Lar\LteAdmin\Core\Delegator;

/**
 * @mixin ChartJsComponent
 */
class ChartJs extends Delegator
{
    protected $class = ChartJsComponent::class;
}
