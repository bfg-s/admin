<?php

namespace Lar\LteAdmin\Core;

use Lar\LteAdmin\Components\Contents\CardContent;
use Lar\LteAdmin\Page;

/**
 * @mixin Page
 */
class PageMixin
{
    public function withTools()
    {
        return function ($test = null) {
            if ($this->hasClass(CardContent::class)) {
                $this->getClass(CardContent::class)->defaultTools($test);
            }

            return $this;
        };
    }
}
