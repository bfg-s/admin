<?php

namespace Lar\LteAdmin\Components\Traits;

use Lar\LteAdmin\Components\TabsComponent;

trait BuildHelperTrait
{
    /**
     * @param  string  $title
     * @param $icon
     * @param  callable|null  $contentCb
     * @param  bool|null  $active
     * @return $this
     */
    public function tab(string $title, $icon = null, callable $contentCb = null, ?bool $active = null)
    {
        $last = $this->last();

        $tabs = $last instanceof TabsComponent ? $last : $this->tabs();

        $tabs->tab($title, $icon, $contentCb, $active);

        return $this;
    }
}
