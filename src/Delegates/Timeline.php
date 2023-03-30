<?php

namespace Admin\Delegates;

use Admin\Components\TimelineComponent;
use Admin\Core\Delegator;

/**
 * @mixin TimelineComponent
 */
class Timeline extends Delegator
{
    protected $class = TimelineComponent::class;
}
