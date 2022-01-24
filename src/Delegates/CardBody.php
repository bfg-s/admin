<?php

namespace Lar\LteAdmin\Delegates;

use Lar\LteAdmin\Components\CardBodyComponent;
use Lar\LteAdmin\Core\Delegator;

/**
 * @mixin CardBodyComponent
 */
class CardBody extends Delegator
{
    protected $class = CardBodyComponent::class;
}
