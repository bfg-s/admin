<?php

namespace Admin\Components;

use Admin\Components\Layout\Logo;
use Admin\Components\Layout\Menu;
use Admin\Components\Layout\MenuBottom;
use Bfg\Layout\View\Component;

/**
 * Class Header
 * @package Admin\Components
 */
class Header extends Component
{
    /**
     * The component alias name.
     *
     * @var string
     */
    public $componentName = "bfg::header";

    /**
     * Inner append content
     */
    public function inner()
    {
        Logo::create();

        Menu::create();

        MenuBottom::create();
    }
}