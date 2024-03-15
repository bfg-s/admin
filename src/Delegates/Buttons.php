<?php

namespace Admin\Delegates;

use Admin\Components\ButtonsComponent;
use Admin\Core\Delegator;
use Illuminate\Support\Traits\Macroable;

/**
 * @mixin ButtonsComponent
 * @mixin MacroMethodsForButtons
 */
class Buttons extends Delegator
{
    use Macroable;

    protected $class = ButtonsComponent::class;

    public function __call($method, $parameters)
    {
        return parent::__call($method, $parameters);
    }
}
