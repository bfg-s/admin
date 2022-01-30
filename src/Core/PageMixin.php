<?php

namespace LteAdmin\Core;

use LteAdmin\Components\CardComponent;
use LteAdmin\Page;

/**
 * @mixin Page
 */
class PageMixin
{
    public function withTools()
    {
        return function ($test = null) {
            if ($this->hasClass(CardComponent::class)) {
                $this->getClass(CardComponent::class)->defaultTools($test);
            }

            return $this;
        };
    }
}
