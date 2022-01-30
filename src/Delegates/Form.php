<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\FormComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin FormComponent
 */
class Form extends Delegator
{
    protected $class = FormComponent::class;
}
