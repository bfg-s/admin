<?php

namespace Admin\Delegates;

use Admin\Components\AlertComponent;
use Admin\Core\Delegator;

/**
 * @mixin AlertComponent
 */
class Alert extends Delegator
{
    protected $class = AlertComponent::class;
}
