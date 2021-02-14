<?php

namespace Admin\Components\ServicePages\Login;

use Bfg\Layout\View\Component;

/**
 * Class Footer
 * @package Admin\Components\ServicePages\Login
 */
class Footer extends Component
{
    /**
     * @var string
     */
    public string $copy;

    /**
     * The component alias name.
     *
     * @var string
     */
    public $componentName = "bfg::servicePages.login.footer";

    /**
     * @var string
     */
    protected static $slotable = "footer";

    /**
     * Footer constructor.
     */
    public function __construct()
    {
        $this->copy = config('admin-ui.footer.copy');
    }
}
