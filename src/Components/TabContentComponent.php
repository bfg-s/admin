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
     * Conditions for active tab.
     *
     * @var mixed|null
     */
    public mixed $activeCondition = null;

    /**
     * Left orientation for tab.
     *
     * @var bool
     */
    public bool $left = true;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'tab-content';

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
     * Set the condition for tab activity.
     *
     * @param $condition
     * @return $this
     */
    public function active($condition): static
    {
        $this->activeCondition = $condition;

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
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        //
    }
}
