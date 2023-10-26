<?php

namespace Admin\Components\ModelTable;

use Admin\Components\Component;

class RowComponent extends Component
{
    protected string $view = 'model-table.row';

    public function __construct(...$delegates)
    {
        parent::__construct($delegates);
    }

    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
