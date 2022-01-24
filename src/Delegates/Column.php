<?php

namespace Lar\LteAdmin\Delegates;

use Lar\LteAdmin\Components\GridColumnComponent;
use Lar\LteAdmin\Core\Delegator;

/**
 * @mixin GridColumnComponent
 */
class Column extends Delegator
{
    protected $class = GridColumnComponent::class;
}
