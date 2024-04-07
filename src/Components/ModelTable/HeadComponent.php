<?php

declare(strict_types=1);

namespace Admin\Components\ModelTable;

use Admin\Components\Component;

class HeadComponent extends Component
{
    protected string $view = 'model-table.head';

    public function __construct(...$delegates)
    {
        parent::__construct($delegates);
    }

    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
