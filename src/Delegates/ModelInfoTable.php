<?php

namespace Admin\Delegates;

use Admin\Components\ModelInfoTableComponent;
use Admin\Core\Delegator;

/**
 * @mixin ModelInfoTableComponent
 */
class ModelInfoTable extends Delegator
{
    protected $class = ModelInfoTableComponent::class;

    public function rowDefault(...$delegates): array
    {
        return [
            $this->id(),
            ...$delegates,
            $this->at(),
        ];
    }
}
