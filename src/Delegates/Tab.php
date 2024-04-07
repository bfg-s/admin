<?php

declare(strict_types=1);

namespace Admin\Delegates;

use Admin\Components\TabContentComponent;
use Admin\Core\Delegator;
use Illuminate\Support\Traits\Macroable;

/**
 * @mixin TabContentComponent
 * @mixin MacroMethodsForTab
 */
class Tab extends Delegator
{
    use Macroable;

    protected $class = TabContentComponent::class;

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
