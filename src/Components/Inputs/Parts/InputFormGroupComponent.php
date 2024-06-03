<?php

declare(strict_types=1);

namespace Admin\Components\Inputs\Parts;

use Admin\Components\Component;

/**
 * Input admin panel for the form group.
 */
class InputFormGroupComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'inputs.parts.input-form-group';

    /**
     * Additional data to be sent to the template.
     *
     * @var array
     */

    protected array $viewData = [];

    /**
     * Set additional data to be sent to the checkbox template.
     *
     * @param  array  $viewData
     * @return void
     */
    public function setViewData(array $viewData): void
    {
        $this->viewData = $viewData;
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return array
     */
    protected function viewData(): array
    {
        return $this->viewData;
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
