<?php

namespace Admin\Delegates;

use Admin\Components\SmallBoxComponent;
use Admin\Core\Delegator;

/**
 * @mixin SmallBoxComponent
 */
class SmallBox extends Delegator
{
    protected $class = SmallBoxComponent::class;
}
