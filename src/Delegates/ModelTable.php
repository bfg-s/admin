<?php

namespace Lar\LteAdmin\Delegates;

use Lar\LteAdmin\Components\ModelTableComponent;
use Lar\LteAdmin\Core\Delegator;

/**
 * @mixin ModelTableComponent
 */
class ModelTable extends Delegator
{
    protected $class = ModelTableComponent::class;
}
