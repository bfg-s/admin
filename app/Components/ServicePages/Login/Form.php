<?php

namespace Admin\Components\ServicePages\Login;

use Admin\Events\AdminLoginEvent;
use Admin\Http\Requests\AdminLoginRequest;
use Bfg\Layout\View\Component;

/**
 * Class Form
 * @package Admin\UI\Components\ServicePages\Login
 */
class Form extends Component
{
    /**
     * The component alias name.
     *
     * @var string
     */
    public $componentName = "aui::servicePages.login.form";

    /**
     * @param  AdminLoginRequest  $request
     */
    public function submit(AdminLoginRequest $request)
    {
        AdminLoginEvent::dispatch($request);
    }
}
