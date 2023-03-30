<?php

namespace Admin\Delegates;

use Admin\Components\TabContentComponent;
use Admin\Core\Delegator;

/**
 * @mixin TabContentComponent
 */
class Tab extends Delegator
{
    protected $class = TabContentComponent::class;
}
