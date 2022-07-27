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

    public function colDefault(...$delegates): array
    {
        return [
            $this->id(),
            ...$delegates,
            $this->colAt(),
        ];
    }

    public function colAt(): array
    {
        return [
            $this->updated_at()->to_hide(),
            $this->created_at(),
        ];
    }
}
