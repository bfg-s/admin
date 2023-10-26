<?php

namespace Admin\Components\ModelTable;

use Admin\Components\Component;

class ColumnComponent extends Component
{
    /**
     * @var string
     */
    protected string $view = 'model-table.column';

    /**
     * @var array
     */
    protected array $viewData = [];

    /**
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct($delegates);
    }

    /**
     * @param  array  $data
     * @return $this
     */
    public function setViewData(array $data): static
    {
        $this->viewData = $data;

        return $this;
    }

    /**
     * @return array
     */
    protected function viewData(): array
    {
        return array_merge([
            'component' => $this
        ], $this->viewData);
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
