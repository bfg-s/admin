<?php

namespace Admin\Delegates;

use Admin\Components\ModelRelationContentComponent;
use Admin\Core\Delegator;

/**
 * @mixin ModelRelationContentComponent
 */
class ModelRelation extends Delegator
{
    protected $class = ModelRelationContentComponent::class;
}
