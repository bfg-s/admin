<?php

namespace Admin\Components\ModelTable;

use Admin\Components\Component;

class HeaderComponent extends Component
{
    protected string $view = 'model-table.header';

    protected array $viewData = [];

    protected bool $fit = false;

    protected $label = null;

    public function __construct(...$delegates)
    {
        parent::__construct($delegates);
    }

    public function setViewData(array $data): static
    {
        $this->viewData = $data;

        return $this;
    }

    public function fit(): static
    {
        $this->fit = true;

        return $this;
    }

    protected function viewData(): array
    {
        return array_merge([
            'label' => $this->label,
            'fit' => $this->fit,
        ], $this->viewData);
    }

    protected function mount(): void
    {
        $this->label = isset($this->viewData['column']['label']) && is_callable($this->viewData['column']['label'])
            ? call_user_func($this->viewData['column']['label'], $this)
            : ($this->viewData['column']['label'] ?? '');
    }
}
