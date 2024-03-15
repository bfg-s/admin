<?php

namespace Admin\Delegates;

use Admin\Components\ModelInfoTableComponent;
use Admin\Core\Delegator;
use Illuminate\Support\Traits\Macroable;

/**
 * @mixin ModelInfoTableComponent
 * @mixin MacroMethodsForModelInfoTable
 */
class ModelInfoTable extends Delegator
{
    use Macroable;

    protected $class = ModelInfoTableComponent::class;

    public function rowDefault(...$delegates): array
    {
        return [
            $this->id(),
            ...$delegates,
            $this->at(),
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
