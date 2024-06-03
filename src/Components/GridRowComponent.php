<?php

declare(strict_types=1);

namespace Admin\Components;

/**
 * Grid row component of the admin panel layout.
 */
class GridRowComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'grid-row';

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
    }
}
