<?php

namespace Lar\LteAdmin\Delegates;

use Lar\LteAdmin\Components\SearchFormComponent;
use Lar\LteAdmin\Core\Delegator;

/**
 * @mixin SearchFormComponent
 */
class SearchForm extends Delegator
{
    protected $class = SearchFormComponent::class;
}
