<?php

namespace Admin\Traits;

use Admin\Components\TabsComponent;

trait BuildHelperTrait
{
    /**
     * @param ...$delegates
     * @return $this
     */
    public function tab(...$delegates)
    {
        $last = $this->last();

        $tabs = $last instanceof TabsComponent ? $last : $this->tabs();

        return $tabs->tab(...$delegates);
    }
}
