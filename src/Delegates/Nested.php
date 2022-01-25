<?php

namespace Lar\LteAdmin\Delegates;

use Lar\LteAdmin\Components\NestedComponent;
use Lar\LteAdmin\Core\Delegator;

/**
 * @mixin NestedComponent
 */
class Nested extends Delegator
{
    protected $class = NestedComponent::class;
}
