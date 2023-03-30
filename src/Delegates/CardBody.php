<?php

namespace Admin\Delegates;

use Admin\Components\CardBodyComponent;
use Admin\Core\Delegator;

/**
 * @mixin CardBodyComponent
 */
class CardBody extends Delegator
{
    protected $class = CardBodyComponent::class;
}
