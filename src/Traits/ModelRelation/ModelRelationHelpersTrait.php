<?php

declare(strict_types=1);

namespace Admin\Traits\ModelRelation;

trait ModelRelationHelpersTrait
{
    /**
     * @param  callable  $call
     * @return $this
     */
    public function empty(callable $call)
    {
        $this->on_empty = $call;

        return $this;
    }
}
