<?php

declare(strict_types=1);

namespace Admin\Delegates;

use Admin\Components\FormComponent;
use Admin\Core\Delegator;
use Closure;
use Illuminate\Support\Traits\Macroable;

/**
 * The delegation that is responsible for the form component.
 *
 * @mixin FormComponent
 * @mixin MacroMethodsForForm
 */
class Form extends Delegator
{
    use Macroable;

    /**
     * Delegated actions for class.
     *
     * @var string
     */
    protected $class = FormComponent::class;

    /**
     * Make default tab general for form.
     *
     * @param ...$delegates
     * @return array
     */
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
