<?php

namespace LteAdmin\Components;

use Lar\Layout\Traits\FontAwesome;

class TabContentComponent extends Component
{
    use FontAwesome;

    /**
     * @var string|null
     */
    public $getTitle = null;
    /**
     * @var string
     */
    public $getIcon = null;
    public $getActiveCondition = null;
    public $getLeft = true;
    /**
     * @var string[]
     */
    protected $props = [
        'tab-pane',
        'role' => 'tabpanel',
    ];

    public function title(string $title)
    {
        $this->getTitle = $title;

        return $this;
    }

    public function right()
    {
        $this->getLeft = false;

        return $this;
    }

    public function active($condition)
    {
        $this->getActiveCondition = $condition;

        return $this;
    }

    public function icon($icon)
    {
        $this->getIcon = $icon;

        return $this;
    }

    protected function mount()
    {
        //
    }
}
