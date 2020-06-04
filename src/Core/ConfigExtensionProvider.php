<?php

namespace Lar\LteAdmin\Core;

use Lar\LteAdmin\ExtendProvider;

/**
 * Class InstallExtensionProvider
 * @package Lar\LteAdmin\Core
 */
class ConfigExtensionProvider {

    /**
     * @var ExtendProvider
     */
    public $provider;

    /**
     * @var array
     */
    protected $scripts = [];

    /**
     * @var array
     */
    protected $styles = [];

    /**
     * ConfigExtensionProvider constructor.
     * @param  ExtendProvider  $provider
     */
    public function __construct(ExtendProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * On boot lte application
     */
    public function boot()
    {

    }

    /**
     * Get extension scripts
     * @return array
     */
    public function getScripts()
    {
        return $this->scripts;
    }

    /**
     * Get extension styles
     * @return array
     */
    public function getStyles()
    {
        return $this->styles;
    }
}