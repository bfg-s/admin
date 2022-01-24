<?php

namespace Lar\LteAdmin\Delegates;

use Lar\LteAdmin\Components\TabContentComponent;
use Lar\LteAdmin\Core\Delegator;

/**
 * @mixin TabContentComponent
 */
class Tab extends Delegator
{
    protected $class = TabContentComponent::class;
}
