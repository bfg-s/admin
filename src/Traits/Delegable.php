<?php

declare(strict_types=1);

namespace Admin\Traits;

use Admin\Core\Delegate;
use Admin\Explanation;

trait Delegable
{
    /**
     * @return static|Delegate
     */
    public static function new(): Delegate|static
    {
        return new Delegate(static::class);
    }

    /**
     * @param ...$delegates
     * @return mixed
     */
    public function newExplainForce(...$delegates): mixed
    {
        return $this->explainForce(Explanation::new($delegates));
    }

    /**
     * @param  Explanation|callable  $explanation
     * @return $this
     */
    public function explainForce($explanation): static
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
     * @param ...$delegates
     * @return mixed
     */
    public function newExplain(...$delegates): mixed
    {
        return $this->explain(Explanation::new($delegates));
    }

    /**
     * @param  Explanation  $explanation
     * @return $this
     */
    public function explain(Explanation $explanation): static
    {
        $explanation->applyFor(static::class, $this);

        return $this;
    }
}
