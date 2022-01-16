<?php

namespace Lar\LteAdmin\Core\Traits;

use Lar\LteAdmin\Core\Delegate;
use Lar\LteAdmin\Explanation;
use Lar\LteAdmin\Segments\Tagable\Form;

trait Delegable
{
    /**
     * @param ...$params
     * @return static|Delegate
     */
    public static function new(...$params)
    {
        return new Delegate(static::class, ...$params);
    }

    public function explain(Explanation $explanation)
    {
        $explanation->applyFor(static::class, $this);

        return $this;
    }

    /**
     * @param Explanation|callable $explanation
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
}
