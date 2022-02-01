<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\SmallBoxComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin SmallBoxComponent
 */
class SmallBox extends Delegator
{
    protected $class = SmallBoxComponent::class;
}
