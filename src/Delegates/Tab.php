<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\TabContentComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin TabContentComponent
 */
class Tab extends Delegator
{
    protected $class = TabContentComponent::class;
}
