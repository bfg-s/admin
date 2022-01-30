<?php

namespace LteAdmin\Traits;

use LteAdmin\Core\Delegate;
use LteAdmin\Explanation;

trait Delegable
{
    /**
     * @return static|Delegate
     */
    public static function new()
    {
        return new Delegate(static::class);
    }

    public function newExplainForce(...$delegates)
    {
        return $this->explainForce(Explanation::new($delegates));
    }

    /**
     * @param  Explanation|callable  $explanation
     * @return $this
     */
    public function explainForce($explanation)
    {
        if (is_callable($explanation)) {
            $explanation = call_user_func($explanation);
        }
        if ($explanation instanceof Explanation) {
            $explanation->applyFor('*', $this);
        }

        return $this;
    }

    public function newExplain(...$delegates)
    {
        return $this->explain(Explanation::new($delegates));
    }

    public function explain(Explanation $explanation)
    {
        $explanation->applyFor(static::class, $this);

        return $this;
    }
}
