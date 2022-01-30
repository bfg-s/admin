<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\NestedComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin NestedComponent
 */
class Nested extends Delegator
{
    protected $class = NestedComponent::class;
}
