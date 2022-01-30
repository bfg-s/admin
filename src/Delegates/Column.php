<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\GridColumnComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin GridColumnComponent
 */
class Column extends Delegator
{
    protected $class = GridColumnComponent::class;
}
