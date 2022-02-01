<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\AlertComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin AlertComponent
 */
class Alert extends Delegator
{
    protected $class = AlertComponent::class;
}
