<?php

declare(strict_types=1);

namespace Admin\Traits;

use Admin\Core\Delegate;
use Admin\Explanation;

/**
 * The trait delegator allows components to handle delegations.
 */
trait Delegable
{
    /**
     * Apply by force new explanations with the specified delegations.
     *
     * @param ...$delegates
     * @return $this
     */
    public function newExplainForce(...$delegates): static
    {
        return $this->explainForce(Explanation::new($delegates));
    }

    /**
     * Apply new explanation.
     *
     * @param  Explanation|callable  $explanation
     * @return $this
     */
    public function explainForce(Explanation|callable $explanation): static
    {
        if (is_callable($explanation)) {

            $explanation = call_user_func($explanation);
        }
        if ($explanation instanceof Explanation) {

            $explanation->applyFor('*', $this);
        }

        return $this;
    }

    /**
     * Create a new delegation for a component.
     *
     * @return static|Delegate
     */
    public static function new(): Delegate|static
    {
        return new Delegate(static::class);
    }

    /**
     * New explanations for these delegations.
     *
     * @param ...$delegates
     * @return $this
     */
    public function newExplain(...$delegates): static
    {
        return $this->explain(Explanation::new($delegates));
    }

    /**
     * Apply the explanation to the current class.
     *
     * @param  Explanation  $explanation
     * @return $this
     */
    public function explain(Explanation $explanation): static
    {
        $explanation->applyFor(static::class, $this);

        return $this;
    }
}
