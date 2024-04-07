<?php

declare(strict_types=1);

namespace Admin\Delegates;

use Admin\Components\ModelTableComponent;
use Admin\Core\Delegator;
use Illuminate\Support\Traits\Macroable;

/**
 * @mixin ModelTableComponent
 * @mixin MacroMethodsForModelTable
 */
class ModelTable extends Delegator
{
    use Macroable;

    protected $class = ModelTableComponent::class;

    public function colDefault(...$delegates): array
    {
        return [
            $this->id(),
            ...$delegates,
            $this->colAt(),
        ];
    }

    public function colAt(): array
    {
        return [
            $this->updated_at()->to_hide(),
            $this->created_at(),
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
