<?php

namespace Lar\LteAdmin\Components\Traits;

use Lar\LteAdmin\Components\TabsComponent;

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
