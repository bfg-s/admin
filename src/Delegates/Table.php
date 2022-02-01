<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\AlertComponent;
use LteAdmin\Components\TableComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin TableComponent
 */
class Table extends Delegator
{
    protected $class = TableComponent::class;
}
