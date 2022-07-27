<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\SearchFormComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin SearchFormComponent
 */
class SearchForm extends Delegator
{
    protected $class = SearchFormComponent::class;

    public function inDefault(...$delegates): array
    {
        return [
            $this->id(),
            ...$delegates,
            $this->created_at(),
        ];
    }
}
