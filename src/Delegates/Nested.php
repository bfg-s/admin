<?php

namespace Admin\Delegates;

use Admin\Components\NestedComponent;
use Admin\Core\Delegator;

/**
 * @mixin NestedComponent
 */
class Nested extends Delegator
{
    protected $class = NestedComponent::class;
}
