<?php

declare(strict_types=1);

namespace Admin\Delegates;

use Admin\Components\SmallBoxComponent;
use Admin\Core\Delegator;
use Closure;
use Illuminate\Support\Traits\Macroable;

/**
 * @mixin SmallBoxComponent
 * @mixin MacroMethodsForSmallBox
 */
class SmallBox extends Delegator
{
    use Macroable;

    protected $class = SmallBoxComponent::class;

    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            $macro = static::$macros[$method];

            if ($macro instanceof Closure) {
                $macro = $macro->bindTo($this, static::class);
            }

            return $macro(...$parameters);
        }

        return parent::__call($method, $parameters);
    }
}
