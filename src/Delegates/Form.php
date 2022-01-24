<?php

namespace Lar\LteAdmin\Delegates;

use Lar\LteAdmin\Components\FormComponent;
use Lar\LteAdmin\Core\Delegator;

/**
 * @mixin FormComponent
 */
class Form extends Delegator
{
    protected $class = FormComponent::class;
}
