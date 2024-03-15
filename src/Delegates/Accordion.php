<?php

namespace Admin\Delegates;

use Admin\Components\AccordionComponent;
use Admin\Core\Delegator;
use Illuminate\Support\Traits\Macroable;

/**
 * @mixin AccordionComponent
 * @mixin MacroMethodsForAccordion
 */
class Accordion extends Delegator
{
    use Macroable;

    protected $class = AccordionComponent::class;

    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {

            $macro = static::$macros[$method];

            if ($macro instanceof \Closure) {
                $macro = $macro->bindTo($this, static::class);
            }

            return $macro(...$parameters);
        }

        return parent::__call($method, $parameters);
    }
}
