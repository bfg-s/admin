<?php

declare(strict_types=1);

namespace Admin\Components\Inputs\Parts;

use Admin\Components\Component;

class InputFormGroupComponent extends Component
{
    protected string $view = 'inputs.parts.input-form-group';

    protected array $viewData = [];

    public function setViewData(array $viewData): void
    {
        $this->viewData = $viewData;
    }

    protected function viewData(): array
    {
        return $this->viewData;
    }

    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
