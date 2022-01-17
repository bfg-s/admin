<?php

namespace Lar\LteAdmin\Core;

use Lar\Developer\Core\Traits\Piplineble;
use Lar\LteAdmin\Controllers\Controller;
use Lar\LteAdmin\Core\Traits\Macroable;
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
     * @var array
     */
    protected $mixins = [];

    /**
     * @var array
     */
    protected $save_pipes = [];

    /**
     * @var array
     */
    protected $delete_pipes = [];

    /**
     * @var array
     */
    protected $pipe_map = [];

    /**
     * The event listener mappings for the lte application.
     * @var array
     */
    protected $listen = [];

    /**
     * The subscriber classes to register.
     * @var array
     */
    protected $subscribe = [];

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
        /** @var Macroable $class */
        foreach ($this->mixins as $class => $mixin) {

            if (is_array($mixin)) {

                foreach ($mixin as $item) {

                    $class::mixin($item);
                }

            } else {

                $class::mixin($mixin);
            }
        }

        /** @var Controller $controller */
        foreach ($this->save_pipes as $controller => $controller_pipe) {

            $controller::pipes($controller_pipe, 'save');
        }

        /** @var Controller $controller */
        foreach ($this->delete_pipes as $controller => $controller_pipe) {

            $controller::pipes($controller_pipe, 'delete');
        }

        /** @var Piplineble $class */
        foreach ($this->pipe_map as $class => $types) {
            foreach ($types as $type => $pipe) {
                $class::pipes($pipe, $type);
            }
        }

        foreach ($this->listen as $event => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                \Event::listen($event, $listener);
            }
        }

        foreach ($this->subscribe as $subscriber) {
            \Event::subscribe($subscriber);
        }
    }

    /**
     * @param  string  $name
     * @param  \Closure  $call
     * @return $this
     */
    public function tableExtension(string $name, \Closure $call)
    {
        ModelTable::addExtension($name, $call);

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
