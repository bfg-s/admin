<?php

namespace Lar\LteAdmin\Delegates;

use Lar\LteAdmin\Components\ModelInfoTableComponent;
use Lar\LteAdmin\Core\Delegator;

/**
 * @mixin ModelInfoTableComponent
 */
class ModelInfoTable extends Delegator
{
    protected $class = ModelInfoTableComponent::class;
}
