<?php

declare(strict_types=1);

namespace Admin\Components\ModelCards;

use Admin\Components\Component;

class CardComponent extends Component
{
    /**
     * @var string
     */
    protected string $view = 'model-cards.card';

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
        return $this->viewData;
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
