<?php

declare(strict_types=1);

namespace Admin\Components;

/**
 * A special component for adding an overlay to the admin panel template.
 */
class OverlayComponent extends Component
{
    /**
     * Final component for the API.
     *
     * @var bool
     */
    protected bool $finallyForApi = true;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'overlay';

    /**
     * Spin for overlay icon component.
     *
     * @var bool
     */
    protected bool $spin = false;

    /**
     * WatchComponent constructor.
     *
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct();
        $this->force_delegates = $delegates;
    }

    /**
     * Set the spin for overlay icon component.
     *
     * @return $this
     */
    public function spin(): static
    {
        $this->spin = true;

        return $this;
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return bool[]
     */
    protected function viewData(): array
    {
        return [
            'spin' => $this->spin,
        ];
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
