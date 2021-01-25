<?php

namespace Admin\Layouts;

use Bfg\Layout\MainLayout;
use Bfg\Layout\MetaConfigs;

/**
 * Class AdminLayout
 * @package Admin\Layouts
 */
abstract class AdminLayout extends MainLayout
{
    /**
     * @var string
     */
    protected $title = "BFG Admin";

    /**
     * @var bool
     */
    protected $ui = true;

    /**
     * @var string
     */
    protected $containerId = "admin-content";

    /**
     * AdminLayout constructor.
     */
    public function __construct()
    {
        /** Inject meta configs */
        $this->makeMetaConfigs();

        /** Inject assets from configs. */
        $this->injectConfigAssets();

        /** Inject theme assets */
        $this->assets();

        /** Inject extensions assets */
        foreach (\AdminExtension::extensions() as $extension) {
            $this->bscripts = array_merge_recursive($this->bscripts, $extension->config()->getScripts());
            $this->styles = array_merge_recursive($this->styles, $extension->config()->getStyles());
        }

        /** Inject admin initializer script */
        $this->bscripts[] = 'vendor/admin/js/admin.js';

        parent::__construct();

        /** Set default favicons */
        $this->makeFavicons();
    }

    /**
     * @param $content
     * @return $this
     */
    public function setContent($content) {

        if (\Admin::guest()) {
            $this->guestTemplate($content);
        } else {
            $this->authTemplate($content);
        }

        return $this;
    }

    /**
     * Set default favicons
     */
    protected function makeFavicons()
    {
        $this->head->link(['rel' => 'icon', 'type' => 'image/png', 'href' => config('admin-ui.logo')]);
        $this->head->link(['rel' => 'apple-touch-icon', 'href' => config('admin-ui.logo')]);
    }

    /**
     * Make a meta configs
     */
    protected function makeMetaConfigs()
    {
        MetaConfigs::add('home_uri', admin_uri());
        MetaConfigs::add('asset', admin_asset());
    }

    /**
     * Inject assets from configs.
     */
    protected function injectConfigAssets()
    {
        $this->styles = array_merge($this->styles, config('admin-ui.plugins.styles'));
        $this->scripts = array_merge($this->scripts, config('admin-ui.plugins.scripts'));
        $this->bscripts = array_merge($this->bscripts, config('admin-ui.plugins.bscripts'));
    }

    /**
     * Inject theme assets. Injected before extensions.
     */
    abstract protected function assets () : void;

    /**
     * Make auth template
     */
    abstract protected function authTemplate ($content) : void;

    /**
     * Inject theme assets. Injected before extensions.
     */
    abstract protected function guestTemplate ($content) : void;
}