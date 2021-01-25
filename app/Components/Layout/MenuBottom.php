<?php

namespace Admin\Components\Layout;

use Bfg\Layout\View\Component;

/**
 * Class MenuBottom
 * @package Admin\Components\Layout
 */
class MenuBottom extends Component
{
    /**
     * The component alias name.
     *
     * @var string
     */
    public $componentName = "bfg::layout.menuBottom";

    /**
     * @var string
     */
    protected static $slotable = 'menu_bottom';
}