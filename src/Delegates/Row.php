<?php

namespace Admin\Delegates;

use Admin\Components\GridRowComponent;
use Admin\Core\Delegator;

/**
 * @mixin GridRowComponent
 */
class Row extends Delegator
{
    protected $class = GridRowComponent::class;
}
