<?php

namespace Admin\Components;

use Bfg\Layout\View\Component;

/**
 * Class Footer
 * @package Admin\Components
 */
class Footer extends Component
{
    /**
     * Footer copy text
     * @var string
     */
    public $copy;

    /**
     * The component alias name.
     *
     * @var string
     */
    public $componentName = "bfg::footer";

    /**
     * Default slot
     * @var string
     */
    static protected $slotable = "footer";

    /**
     * Footer constructor.
     */
    public function __construct()
    {
        $this->copy = config('admin-ui.footer.copy');
    }
}