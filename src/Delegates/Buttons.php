<?php

namespace Lar\LteAdmin\Delegates;

use Lar\LteAdmin\Components\ButtonsComponent;
use Lar\LteAdmin\Core\Delegator;

/**
 * @mixin ButtonsComponent
 */
class Buttons extends Delegator
{
    protected $class = ButtonsComponent::class;
}
