<?php

namespace Admin\Delegates;

use Admin\Components\ModalComponent;
use Admin\Core\Delegator;
use Illuminate\Support\Traits\Macroable;

/**
 * @mixin ModalComponent
 * @mixin MacroMethodsForModal
 */
class Modal extends Delegator
{
    use Macroable;

    protected $class = ModalComponent::class;

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
