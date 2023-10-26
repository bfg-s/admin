<?php

namespace Admin\Components\ModelTable;

use Admin\Components\Component;

class BodyComponent extends Component
{
    protected string $view = 'model-table.body';

    public function __construct(...$delegates)
    {
        parent::__construct($delegates);
    }

    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
