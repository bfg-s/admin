<?php

namespace Lar\LteAdmin\Delegates;

use Lar\LteAdmin\Components\ModelRelationComponent;
use Lar\LteAdmin\Core\Delegator;

/**
 * @mixin ModelRelationComponent
 */
class ModelRelation extends Delegator
{
    protected $class = ModelRelationComponent::class;
}
