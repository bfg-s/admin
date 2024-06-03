<?php

declare(strict_types=1);

namespace Admin\Components;

/**
 * A special observer component for live parts of the admin panel template.
 */
class WatchComponent extends LiveComponent
{
    /**
     * WatchComponent constructor.
     *
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
