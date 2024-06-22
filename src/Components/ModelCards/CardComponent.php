<?php

declare(strict_types=1);

namespace Admin\Components\ModelCards;

use Admin\Components\Component;

/**
 * Built-in admin panel card.
 */
class CardComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'model-cards.card';

    /**
     * Additional data to be sent to the template.
     *
     * @var array
     */
    protected array $viewData = [];

    /**
     * CardComponent constructor.
     *
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct($delegates);
    }

    /**
     * Set additional data to be sent to the card template.
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
        return $this->viewData;
    }

    /**
     * Additional data to be sent to the API.
     *
     * @return array
     */
    protected function apiData(): array
    {
        return [];
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
