<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\ModalBodyComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin ModalBodyComponent
 */
class ModalBody extends Delegator
{
    protected $class = ModalBodyComponent::class;
}
