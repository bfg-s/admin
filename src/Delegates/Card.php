<?php

namespace Lar\LteAdmin\Delegates;

use Lar\LteAdmin\Components\CardComponent;
use Lar\LteAdmin\Core\Delegator;

/**
 * @mixin CardComponent
 */
class Card extends Delegator
{
    protected $class = CardComponent::class;
}
