<?php

declare(strict_types=1);

namespace Admin\Traits;

use Admin\Components\TabsComponent;

/**
 * Trait assistant that adds the ability to easily work with tabs.
 */
trait ComponentTabsTrait
{
    /**
     * Create a tab and a tab component if it does not already exist in the latest content.
     *
     * @param ...$delegates
     * @return $this|TabsComponent
     */
    public function tab(...$delegates): static|TabsComponent
    {
        $last = $this->last();

        $tabs = $last instanceof TabsComponent ? $last : $this->tabs();

        return $tabs->tab(...$delegates);
    }

    /**
     * Get the latest content component.
     *
     * @return mixed|null
     */
    public function last(): mixed
    {
        if (count($this->contents)) {

            return $this->contents[array_key_last($this->contents)] ?? null;
        }
        return null;
    }
}
