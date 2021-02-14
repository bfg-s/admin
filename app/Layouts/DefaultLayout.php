<?php

namespace Admin\Layouts;

use Admin\Components\Footer;
use Admin\Components\Header;
use Admin\Components\Layout\Logo;
use Admin\Components\Layout\Menu;
use Admin\Components\Layout\MenuBottom;
use Admin\Components\Wrapper;

/**
 * Class DefaultLayout
 * @package Admin\Layouts
 */
class DefaultLayout extends AdminLayout {

    /**
     * @var \string[][]
     */
    protected $metas = [
        ['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0']
    ];

    /**
     * Inject theme assets. Injected before extensions.
     */
    protected function assets(): void
    {
        $this->styles[] = "vendor/admin/theme/default/plugins/bootstrap-icons.css";
        $this->styles[] = "vendor/admin/theme/default/theme.css";

        $this->bscripts[] = "vendor/admin/theme/default/plugins/bootstrap/js/bootstrap.min.js";
        $this->bscripts[] = "vendor/admin/theme/default/theme.js";
    }

    /**
     * @param $content
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function authTemplate($content): void
    {
        $this->body->attr('class', 'app');

        $this->body->text(
            Header::create()
        );

        $this->body->text(
            Wrapper::create(['content' => $content])
        );
    }

    /**
     * @param $content
     */
    protected function guestTemplate($content): void
    {
        $this->body
            ->attr('class', 'app app-login p-0')
            ->appEnd($content);
    }
}