<?php

namespace Admin\Delegates;

use Admin\Components\NestedComponent;
use Admin\Core\Delegator;
use Illuminate\Support\Traits\Macroable;

/**
 * @mixin NestedComponent
 * @mixin MacroMethodsForNested
 */
class Nested extends Delegator
{
    use Macroable;

    protected $class = NestedComponent::class;

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
