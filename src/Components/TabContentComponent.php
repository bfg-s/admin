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
     * The padding of the tab content.
     *
     * @var int|null
     */
    protected int|null $padding = null;

    /**
     * The right padding of the tab content.
     *
     * @var int|null
     */
    protected int|null $paddingRight = null;

    /**
     * Set tab content padding right.
     *
     * @param  int  $padding
     * @return $this
     */
    public function paddingRight(int $padding): static
    {
        $this->paddingRight = $padding;

        return $this;
    }

    /**
     * Set tab content padding.
     *
     * @param  int  $padding
     * @return $this
     */
    public function padding(int $padding): static
    {
        $this->padding = $padding;

        return $this;
    }

    /**
     * Set tab id.
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
     * Get Tab content id.
     *
     * @return string|null
     */
    public function getId(): string|null
    {
        return $this->id;
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
     * Get tab content title.
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
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
     * @param  string|null  $name
     * @return $this
     */
    public function icon(string|null $name): static
    {
        $this->icon = $name;

        return $this;
    }

    /**
     * Get tab content icon.
     *
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Get component contents.
     *
     * @return array
     */
    public function getContents(): array
    {
        return $this->contents;
    }

    /**
     * Get component attributes.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
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
            'icon' => $this->icon,
            'title' => $this->title,
            'active' => $this->active,
            'padding' => $this->padding,
            'paddingRight' => $this->paddingRight,
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
