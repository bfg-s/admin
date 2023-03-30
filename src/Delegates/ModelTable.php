<?php

namespace Admin\Delegates;

use Admin\Components\ModelTableComponent;
use Admin\Core\Delegator;

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
