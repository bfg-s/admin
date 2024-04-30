<?php

declare(strict_types=1);

namespace Admin\Delegates;

use Admin\Components\SearchFormComponent;
use Admin\Core\Delegator;
use Illuminate\Support\Traits\Macroable;

/**
 * @mixin SearchFormComponent
 * @mixin MacroMethodsForSearchForm
 */
class SearchForm extends Delegator
{
    use Macroable;

    protected $class = SearchFormComponent::class;

    public function inDefault(...$delegates): array
    {
        return [
            $this->id(),
            ...$delegates,
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
