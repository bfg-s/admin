<?php

namespace Lar\LteAdmin\Core;

use Lar\LteAdmin\Components\CardComponent;
use Lar\LteAdmin\Page;

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
