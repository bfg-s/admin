<?php

namespace Admin\Delegates;

use Admin\Components\GridColumnComponent;
use Admin\Core\Delegator;

/**
 * @mixin GridColumnComponent
 */
class Column extends Delegator
{
    protected $class = GridColumnComponent::class;
}
