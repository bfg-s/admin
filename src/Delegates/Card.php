<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\CardComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin CardComponent
 */
class Card extends Delegator
{
    protected $class = CardComponent::class;
}
