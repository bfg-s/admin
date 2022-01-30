<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\ModelTableComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin ModelTableComponent
 */
class ModelTable extends Delegator
{
    protected $class = ModelTableComponent::class;
}
