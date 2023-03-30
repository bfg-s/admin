<?php

namespace Admin\Delegates;

use Admin\Components\ModalComponent;
use Admin\Core\Delegator;

/**
 * @mixin ModalComponent
 */
class Modal extends Delegator
{
    protected $class = ModalComponent::class;
}
