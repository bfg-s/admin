<?php

namespace Admin\Components;

class GridRowComponent extends Component
{
    /**
     * @var string
     */
    protected string $view = 'grid-row';

    /**
     * @return voids
     */
    protected function mount(): void
    {
        $this->newExplainForce($this->delegates);
        $this->newExplainForce($this->force_delegates);
//        if (!$this->iSelectModel && ($this->parent?->model ?? null)) {
//            $this->model($this->parent->model);
//        }
    }
}
