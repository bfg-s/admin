<?php

namespace Admin\Delegates;

use Admin\Components\TableComponent;
use Admin\Core\Delegator;

/**
 * @mixin TableComponent
 */
class Table extends Delegator
{
    protected $class = TableComponent::class;
}
