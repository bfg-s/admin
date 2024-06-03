<?php

declare(strict_types=1);

namespace Admin\Components\ModelTable;

use Admin\Components\Component;

/**
 * Head of the admin panel component for the model table.
 */
class HeadComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'model-table.head';

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
