<?php

namespace Admin\Layouts;

use Bfg\Layout\MainLayout;

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
        [""]
    ];

    /**
     * DefaultAdminLayout constructor.
     * @param  mixed  ...$params
     */
    public function __construct(...$params)
    {
        $this->styles[] = 'css/admin.css';

        foreach (\AdminExtension::extensions() as $extension) {

            $this->bscripts = array_merge_recursive($this->bscripts, $extension->config()->getScripts());

            $this->styles = array_merge_recursive($this->styles, $extension->config()->getStyles());
        }

        $this->bscripts[] = 'js/admin.js';

        parent::__construct($params);

        $this->head->link(['rel' => 'icon', 'type' => 'image/png', 'href' => admin_asset('images/favicon.png')]);

        $this->head->link(['rel' => 'apple-touch-icon', 'href' => admin_asset('images/favicon.png')]);
    }
}