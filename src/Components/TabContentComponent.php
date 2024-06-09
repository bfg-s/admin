<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Traits\FontAwesomeTrait;

/**
 * Tab contact component of the admin panel.
 */
class TabContentComponent extends Component
{
    use FontAwesomeTrait;

    /**
     * Tab title.
     *
     * @var string|null
     */
    public string|null $title = null;

    /**
     * Tab icon.
     *
     * @var string|null
     */
    public string|null $icon = null;

    /**
     * Left orientation for tab.
     *
     * @var bool
     */
    public bool $left = true;

    /**
     * Tab id.
     *
     * @var string|null
     */
    protected string|null $id = null;

    /**
     * Vertical orientation for tab.
     *
     * @var bool
     */
    protected bool $vertical = true;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'tab-content';

    /**
     * Is the tab active.
     *
     * @var bool|null
     */
    protected bool|null $active = null;

    /**
     * Set  tab id.
     *
     * @param  string  $id
     * @return $this
     */
    public function id(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Active status of the tab.
     *
     * @param  bool  $active
     * @return $this
     */
    public function active(bool $active = true): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Check if the tab is active.
     *
     * @return bool|null
     */
    public function isActive(): bool|null
    {
        return $this->active;
    }

    /**
     * Set tab title.
     *
     * @param  string  $title
     * @return $this
     */
    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set tab orientation to rights.
     *
     * @return $this
     */
    public function right(): static
    {
        $this->left = false;

        return $this;
    }

    /**
     * Set tab icon.
     *
     * @param  string  $name
     * @return $this
     */
    public function icon(string $name): static
    {
        $this->icon = $name;

        return $this;
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return string[]
     */
    protected function viewData(): array
    {
        return [
            'id' => $this->id,
            'active' => $this->active,
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        //
    }
}
