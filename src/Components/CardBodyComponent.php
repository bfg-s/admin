<?php

namespace Admin\Components;

class CardBodyComponent extends Component
{
    /**
     * @var string
     */
    protected string $view = 'card-body';

    /**
     * @var bool
     */
    protected bool $foolSpace = false;

    /**
     * @var bool
     */
    protected bool $tableResponsive = false;

    /**
     * @return $this
     */
    public function fullSpace(): static
    {
        $this->foolSpace = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function tableResponsive(): static
    {
        $this->tableResponsive = true;

        return $this;
    }

    /**
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'foolSpace' => $this->foolSpace,
            'tableResponsive' => $this->tableResponsive,
        ];
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
    }
}
