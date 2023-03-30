<?php

namespace Admin\Core;

use Closure;
use Event;
use Admin\Components\Component;
use Admin\Components\ModelTableComponent;
use Admin\Controllers\Controller;
use Admin\ExtendProvider;
use Admin\Traits\Macroable;
use Admin\Traits\Piplineble;

class ConfigExtensionProvider
{
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
     * The event listener mappings for the application.
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
     * On boot application.
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
                Event::listen($event, $listener);
            }
        }

        foreach ($this->subscribe as $subscriber) {
            Event::subscribe($subscriber);
        }
    }

    /**
     * @param  string  $name
     * @param  Closure  $call
     * @return $this
     */
    public function tableExtension(string $name, Closure $call)
    {
        ModelTableComponent::addExtension($name, $call);

        return $this;
    }

    /**
     * @param  string  $class
     * @return $this
     */
    public function tableExtensionClass(string $class)
    {
        ModelTableComponent::addExtensionClass($class);

        return $this;
    }

    /**
     * @param  string  $name
     * @param  string  $class
     * @return $this
     */
    public function formField(string $name, string $class)
    {
        Component::registerFormComponent($name, $class);

        return $this;
    }

    /**
     * Get extension scripts.
     * @return array
     */
    public function getScripts()
    {
        return $this->scripts;
    }

    /**
     * Get extension styles.
     * @return array
     */
    public function getStyles()
    {
        return $this->styles;
    }
}
