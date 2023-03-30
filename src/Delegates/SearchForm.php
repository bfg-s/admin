<?php

namespace Admin\Delegates;

use Admin\Components\SearchFormComponent;
use Admin\Core\Delegator;

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
