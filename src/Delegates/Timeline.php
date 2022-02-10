<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\TimelineComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin TimelineComponent
 */
class Timeline extends Delegator
{
    protected $class = TimelineComponent::class;
}
