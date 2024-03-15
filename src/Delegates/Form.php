<?php

namespace Admin\Delegates;

use Admin\Components\FormComponent;
use Admin\Core\Delegator;
use Illuminate\Support\Traits\Macroable;

/**
 * @mixin FormComponent
 * @mixin MacroMethodsForForm
 */
class Form extends Delegator
{
    use Macroable;

    protected $class = FormComponent::class;

    public function tabGeneral(...$delegates): array
    {
        $tab = new Tab();

        return [
            $this->tab(
                $tab->title(__('admin.general'))->icon_globe(),
                $tab->inputInfoId(),
                [...$delegates],
                $tab->inputInfoAt(),
            )
        ];
    }

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
