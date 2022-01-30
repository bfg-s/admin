<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\ButtonsComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin ButtonsComponent
 */
class Buttons extends Delegator
{
    protected $class = ButtonsComponent::class;
}
