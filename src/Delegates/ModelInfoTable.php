<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\ModelInfoTableComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin ModelInfoTableComponent
 */
class ModelInfoTable extends Delegator
{
    protected $class = ModelInfoTableComponent::class;
}
