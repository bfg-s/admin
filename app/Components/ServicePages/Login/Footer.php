<?php

namespace Admin\Components\ServicePage\Login;

use Bfg\Layout\View\Component;

/**
 * Class Footer
 * @package Admin\Components\ServicePage\Login
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
    public $componentName = "aui::servicePages.login.footer";

    /**
     * Footer constructor.
     */
    public function __construct()
    {
        $this->copy = config('admin-ui.footer.copy');
    }
}
