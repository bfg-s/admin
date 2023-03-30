<?php

namespace Admin\Delegates;

use Admin\Components\ButtonsComponent;
use Admin\Core\Delegator;

/**
 * @mixin ButtonsComponent
 */
class Buttons extends Delegator
{
    protected $class = ButtonsComponent::class;
}
