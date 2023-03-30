<?php

namespace Admin\Delegates;

use Admin\Components\ModalBodyComponent;
use Admin\Core\Delegator;

/**
 * @mixin ModalBodyComponent
 */
class ModalBody extends Delegator
{
    protected $class = ModalBodyComponent::class;
}
