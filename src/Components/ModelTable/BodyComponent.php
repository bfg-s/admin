<?php

declare(strict_types=1);

namespace Admin\Components\ModelTable;

use Admin\Components\Component;

/**
 * The body of the admin panel component for the model table.
 */
class BodyComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'model-table.body';

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
