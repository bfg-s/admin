<?php

declare(strict_types=1);

namespace Admin\Components\ModelTable;

use Admin\Components\Component;

/**
 * Header of the admin panel component for the model table.
 */
class HeaderComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'model-table.header';

    /**
     * Additional data to be sent to the template.
     *
     * @var array
     */
    protected array $viewData = [];

    /**
     * Add the "fit" class.
     *
     * @var bool
     */
    protected bool $fit = false;

    /**
     * Title label.
     *
     * @var string|null
     */
    protected string|null $label = null;

    /**
     * Set additional data to be sent to the column template.
     *
     * @param  array  $data
     * @return $this
     */
    public function setViewData(array $data): static
    {
        $this->viewData = $data;

        return $this;
    }

    /**
     * Enable adding "fit" class.
     *
     * @return $this
     */
    public function fit(): static
    {
        $this->fit = true;

        return $this;
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return array
     */
    protected function viewData(): array
    {
        return array_merge([
            'label' => $this->label,
            'fit' => $this->fit,
        ], $this->viewData);
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        $this->label = isset($this->viewData['column']['label'])
        && is_callable($this->viewData['column']['label'])
        && !is_string($this->viewData['column']['label'])
            ? call_user_func($this->viewData['column']['label'], $this)
            : ($this->viewData['column']['label'] ?? '');
    }
}
