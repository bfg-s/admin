<?php

declare(strict_types=1);

namespace Admin\Delegates;

use Admin\Components\ModalComponent;
use Admin\Core\Delegator;
use Closure;
use Illuminate\Support\Traits\Macroable;

/**
 * The delegation that is responsible for the modal window component.
 *
 * @mixin ModalComponent
 * @mixin MacroMethodsForModal
 */
class Modal extends Delegator
{
    use Macroable;

    /**
     * Delegated actions for class.
     *
     * @var string
     */
    protected $class = ModalComponent::class;

    /**
     * Magic method for macros.
     *
     * @param $method
     * @param $parameters
     * @return \Admin\Core\Delegate|mixed
     */
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
