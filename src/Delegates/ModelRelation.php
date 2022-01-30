<?php

namespace LteAdmin\Delegates;

use LteAdmin\Components\ModelRelationContentComponent;
use LteAdmin\Core\Delegator;

/**
 * @mixin ModelRelationContentComponent
 */
class ModelRelation extends Delegator
{
    protected $class = ModelRelationContentComponent::class;
}
