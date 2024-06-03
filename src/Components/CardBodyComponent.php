<?php

declare(strict_types=1);

namespace Admin\Components;

/**
 * The component that is responsible for the body of the card.
 */
class CardBodyComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'card-body';

    /**
     * The mark is responsible for ensuring that the body is the entire width and height of the card, without indents.
     *
     * @var bool
     */
    protected bool $foolSpace = false;

    /**
     * Label responsible for the table scrolling mode in different directions if the table is very wide.
     *
     * @var bool
     */
    protected bool $tableResponsive = false;

    /**
     * Make the body the entire width and height of the card.
     *
     * @return $this
     */
    public function fullSpace(): static
    {
        $this->foolSpace = true;

        return $this;
    }

    /**
     * Make the body stretch, allows you to accommodate a very wide table.
     *
     * @return $this
     */
    public function tableResponsive(): static
    {
        $this->tableResponsive = true;

        return $this;
    }

    /**
     * Additional data to be sent to the template.
     *
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
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
    }
}
