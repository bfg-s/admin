<?php

namespace Lar\LteAdmin\Components;

class WatchComponent extends LiveComponent
{
    /**
     * @param $condition
     * @param ...$delegates
     */
    public function __construct($condition, ...$delegates)
    {
        parent::__construct();
        if ($condition) {
            $this->force_delegates = $delegates;
        }
    }
}
