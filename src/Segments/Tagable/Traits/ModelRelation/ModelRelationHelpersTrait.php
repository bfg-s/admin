<?php

namespace Lar\LteAdmin\Segments\Tagable\Traits\ModelRelation;

/**
 * Trait ModelRelationHelpersTrait
 * @package Lar\LteAdmin\Segments\Tagable\Traits\ModelRelation
 */
trait ModelRelationHelpersTrait {

    /**
     * @param array|\Closure $instruction
     */
    public function model($instruction)
    {
        $this->model_instruction = array_merge($this->model_instruction, (array)$instruction);
    }
}