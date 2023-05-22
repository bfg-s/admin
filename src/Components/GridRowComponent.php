<?php

namespace Admin\Components;

class GridRowComponent extends Component
{
    /**
     * @var string[]
     */
    protected $props = [
        'row',
    ];

    protected function mount()
    {
        // TODO: Implement mount() method.
    }

    /**
     * @return mixed|void
     */
    public function onRender()
    {
        $this->newExplainForce($this->delegates);
        $this->newExplainForce($this->force_delegates);
        if (!$this->iSelectModel && ($this->parent?->model ?? null)) {
            $this->model($this->parent->model);
        }
        $this->mount();
        $this->callRenderEvents();
    }
}
