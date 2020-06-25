<?php

namespace Lar\LteAdmin\Core;

use Lar\LteAdmin\ExtendProvider;
use Lar\LteAdmin\Segments\Tagable\Field;
use Lar\LteAdmin\Segments\Tagable\ModelTable;

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
     * @param  string  $name
     * @param  \Closure  $closure
     * @return $this
     */
    public function tableExtension(string $name, \Closure $closure)
    {
        ModelTable::addExtension($name, $closure);

        return $this;
    }

    /**
     * @param  string  $class
     * @return $this
     */
    public function tableExtensionClass(string $class)
    {
        ModelTable::addExtensionClass($class);

        return $this;
    }

    /**
     * @param  string  $name
     * @param  string  $class
     * @return $this
     */
    public function formField(string $name, string $class)
    {
        Field::registerFormComponent($name, $class);

        return $this;
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