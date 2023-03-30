<?php

namespace Admin\Components;

class CardBodyComponent extends Component
{
    /**
     * @var string[]
     */
    protected $props = [
        'card-body',
    ];

    public function fullSpace()
    {
        $this->addClass('p-0');

        return $this;
    }

    protected function mount()
    {
    }
}
