<?php

namespace Admin\Layouts;

use Bfg\Layout\MainLayout;
use Bfg\Layout\MetaConfigs;

/**
 * Class DefaultAdminLayout
 * @package Admin\Layouts
 */
class DefaultAdminLayout extends MainLayout {

    /**
     * @var string
     */
    protected $title = "BFG Admin";

    /**
     * @var string
     */
    protected $asset_driver = "admin_asset";

    /**
     * @var \string[][]
     */
    protected $metas = [
        ['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0']
    ];

    /**
     * DefaultAdminLayout constructor.
     * @param  mixed  ...$params
     */
    public function __construct()
    {
        MetaConfigs::add('home_uri', admin_uri());
        MetaConfigs::add('asset', admin_asset());

        $this->styles[] = 'css/admin.css';

        $this->ui();

        foreach (\AdminExtension::extensions() as $extension) {

            $this->bscripts = array_merge_recursive($this->bscripts, $extension->config()->getScripts());

            $this->styles = array_merge_recursive($this->styles, $extension->config()->getStyles());
        }

        $this->bscripts[] = 'js/admin.js';

        parent::__construct();

        $this->head->link(['rel' => 'icon', 'type' => 'image/png', 'href' => admin_asset('images/bfg-logo.png')]);

        $this->head->link(['rel' => 'apple-touch-icon', 'href' => admin_asset('images/bfg-logo.png')]);
    }

    /**
     * UI Settings injection
     */
    protected function ui()
    {
        $theme = config('admin-ui.theme');

        $this->styles[] = "theme/{$theme}/theme.css";

        $this->styles = array_merge($this->styles, config('admin-ui.plugins.styles'));

        $this->scripts = array_merge($this->scripts, config('admin-ui.plugins.scripts'));

        $this->scripts[] = "theme/{$theme}/theme.js";
    }
}