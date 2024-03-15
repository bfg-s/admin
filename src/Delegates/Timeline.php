<?php

namespace Admin\Delegates;

use Admin\Components\TimelineComponent;
use Admin\Core\Delegator;
use Illuminate\Support\Traits\Macroable;

/**
 * @mixin TimelineComponent
 * @mixin MacroMethodsForTimeline
 */
class Timeline extends Delegator
{
    use Macroable;

    protected $class = TimelineComponent::class;

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
