<?php

declare(strict_types=1);

namespace Admin\Delegates;

use Admin\Components\ModelInfoTableComponent;
use Admin\Core\Delegator;
use Closure;
use Illuminate\Support\Traits\Macroable;

/**
 * The delegation that is responsible for the component of the model info table.
 *
 * @mixin ModelInfoTableComponent
 * @mixin MacroMethodsForModelInfoTable
 */
class ModelInfoTable extends Delegator
{
    use Macroable;

    /**
     * Delegated actions for class.
     *
     * @var string
     */
    protected $class = ModelInfoTableComponent::class;

    /**
     * Make default rows for model info table.
     *
     * @param ...$delegates
     * @return array
     */
    public function rowDefault(...$delegates): array
    {
        return [
            $this->id(),
            ...$delegates,
            $this->at(),
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
