<?php

namespace Admin\Traits;

use Admin\Components\TabsComponent;

trait BuildHelperTrait
{
    /**
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
