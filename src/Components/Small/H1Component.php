<?php

declare(strict_types=1);

namespace Admin\Components\Small;

use Admin\Components\Component;

/**
 * The HTML component of the "h1" tag.
 */
class H1Component extends Component
{
    /**
     * The tag element from which the component begins.
     *
     * @var string
     */
    protected string $element = 'h1';

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
