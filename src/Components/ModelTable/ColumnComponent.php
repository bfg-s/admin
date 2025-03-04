<?php

declare(strict_types=1);

namespace Admin\Components\ModelTable;

use Admin\Components\Component;

/**
 * Admin panel component for grid columns.
 */
class ColumnComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'model-table.column';

    /**
     * Additional data to be sent to the template.
     *
     * @var array
     */
    protected array $viewData = [];

    /**
     * Hide the column on mobile devices.
     *
     * @var bool
     */
    protected bool $hideOnMobile = false;

    /**
     * Hide the column on mobile devices.
     *
     * @return $this
     */
    public function hideOnMobile(): static
    {
        $this->hideOnMobile = true;

        return $this;
    }

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
     * Additional data to be sent to the template.
     *
     * @return array
     */
    protected function viewData(): array
    {
        return array_merge([
            'hideOnMobile' => $this->hideOnMobile,
        ],$this->viewData);
    }

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
