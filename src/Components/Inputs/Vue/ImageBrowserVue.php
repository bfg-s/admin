<?php

declare(strict_types=1);

namespace Admin\Components\Inputs\Vue;

use Admin\Components\Vue\Vue;

/**
 * VueJs input admin panel for viewing pictures.
 */
class ImageBrowserVue extends Vue
{
    /**
     * The tag element from which the component begins.
     *
     * @var string
     */
    protected string $element = 'bfg-browser-component';
}
