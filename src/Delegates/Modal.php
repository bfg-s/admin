<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\ModalComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin ModalComponent
 */
class Modal extends Delegator
{
    protected $class = ModalComponent::class;
}
