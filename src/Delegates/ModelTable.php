<?php

declare(strict_types=1);

namespace Admin\Delegates;

use Admin\Components\ModelTableComponent;
use Admin\Core\Delegator;
use Closure;
use Illuminate\Support\Traits\Macroable;

/**
 * The delegation that is responsible for the model table component.
 *
 * @mixin ModelTableComponent
 * @mixin MacroMethodsForModelTable
 */
class ModelTable extends Delegator
{
    use Macroable;

    /**
     * Delegated actions for class.
     *
     * @var string
     */
    protected $class = ModelTableComponent::class;

    /**
     * Make default columns for model table.
     *
     * @param ...$delegates
     * @return array
     */
    public function colDefault(...$delegates): array
    {
        return [
            $this->id(),
            ...$delegates,
            $this->colAt(),
        ];
    }

    /**
     * Make default "at" columns for model table.
     *
     * @return array
     */
    public function colAt(): array
    {
        return [
            $this->updated_at()->to_hide(),
            $this->created_at(),
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
