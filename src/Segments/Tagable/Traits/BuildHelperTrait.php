<?php

namespace Lar\LteAdmin\Segments\Tagable\Traits;

use Lar\LteAdmin\Segments\Tagable\Tabs;

/**
 * Trait BuildHelperTrait.
 * @package Lar\LteAdmin\Segments\Tagable\Traits
 */
trait BuildHelperTrait
{
    /**
     * @param  string  $title
     * @param  string|mixed  $icon
     * @param  array  $attrs
     * @return $this
     */
    public function tab(string $title, $icon = null, ...$attrs)
    {
        $last = $this->last();

        $tabs = $last instanceof Tabs ? $last : $this->tabs();

        $tabs->tab($title, $icon, ...$attrs);

        return $this;
    }
}
