<?php

namespace Admin\Components\ServicePages\Login;

use Admin\Events\AdminLoginEvent;
use Admin\Http\Requests\AdminLoginRequest;
use Admin\Http\Resources\AdminAuthResource;
use Admin\Http\Resources\AdminMenuResource;
use Admin\Models\AdminPage;
use Bfg\Dev\Support\Behavior\EmbeddedAttributes\Action;
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
    public $componentName = "bfg::servicePages.login.form";

    /**
     * Admin auth action
     */
    #[Action(
        AdminLoginRequest::class,
        AdminLoginEvent::class,
        AdminAuthResource::class,
    )] public function submit() {}
}
