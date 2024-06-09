<?php

declare(strict_types=1);

namespace Admin\Components;

/**
 * Dummy field component of the admin panel.
 */
class DummyComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'content-only';

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        //
    }
}
