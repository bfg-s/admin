<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\CardBodyComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin CardBodyComponent
 */
class CardBody extends Delegator
{
    protected $class = CardBodyComponent::class;
}
