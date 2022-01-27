<?php

namespace Lar\LteAdmin\Delegates;

use Lar\LteAdmin\Components\ModelRelationContentComponent;
use Lar\LteAdmin\Core\Delegator;

/**
 * @mixin ModelRelationContentComponent
 */
class ModelRelation extends Delegator
{
    protected $class = ModelRelationContentComponent::class;
}
