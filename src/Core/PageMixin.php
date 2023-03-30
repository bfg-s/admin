<?php

namespace Admin\Core;

use Admin\Components\CardComponent;
use Admin\Page;

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
