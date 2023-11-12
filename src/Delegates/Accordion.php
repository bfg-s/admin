<?php

namespace Admin\Delegates;

use Admin\Components\AccordionComponent;
use Admin\Core\Delegator;

/**
 * @mixin AccordionComponent
 */
class Accordion extends Delegator
{
    protected $class = AccordionComponent::class;
}
