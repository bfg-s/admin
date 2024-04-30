<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Traits\FontAwesome;

class TabContentComponent extends Component
{
    use FontAwesome;

    /**
     * @var string|null
     */
    public ?string $getTitle = null;

    /**
     * @var string|null
     */
    public ?string $getIcon = null;

    /**
     * @var mixed|null
     */
    public mixed $getActiveCondition = null;

    /**
     * @var bool
     */
    public bool $getLeft = true;

    /**
     * @var string
     */
    protected string $view = 'tab-content';

    /**
     * @param  string  $title
     * @return $this
     */
    public function title(string $title): static
    {
        $this->getTitle = $title;

        return $this;
    }

    /**
     * @return $this
     */
    public function right(): static
    {
        $this->getLeft = false;

        return $this;
    }

    /**
     * @param $condition
     * @return $this
     */
    public function active($condition): static
    {
        $this->getActiveCondition = $condition;

        return $this;
    }

    /**
     * @param  string  $name
     * @return $this
     */
    public function icon(string $name): static
    {
        $this->getIcon = $name;

        return $this;
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
        //
    }
}
