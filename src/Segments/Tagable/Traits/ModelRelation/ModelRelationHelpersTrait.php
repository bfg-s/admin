<?php

namespace Lar\LteAdmin\Segments\Tagable\Traits\ModelRelation;

/**
 * Trait ModelRelationHelpersTrait.
 * @package Lar\LteAdmin\Segments\Tagable\Traits\ModelRelation
 */
trait ModelRelationHelpersTrait
{
    /**
     * @param  array|\Closure  $instruction
     * @return $this
     */
    public function model($instruction)
    {
        $this->model_instruction = array_merge($this->model_instruction, (array) $instruction);

        return $this;
    }

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
