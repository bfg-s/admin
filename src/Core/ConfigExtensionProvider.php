<?php

namespace Admin\Core;

use Closure;
use Admin\Components\Component;
use Admin\Components\ModelTableComponent;
use Admin\ExtendProvider;

class ConfigExtensionProvider
{
    /**
     * @var ExtendProvider
     */
    public ExtendProvider $provider;

    /**
     * @var array
     */
    protected array $scripts = [];

    /**
     * @var array
     */
    protected array $styles = [];

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

    }

    /**
     * @param  string  $name
     * @param  Closure  $call
     * @return $this
     */
    public function tableExtension(string $name, Closure $call): static
    {
        ModelTableComponent::addExtension($name, $call);

        return $this;
    }

    /**
     * @param  string  $class
     * @return $this
     */
    public function tableExtensionClass(string $class): static
    {
        ModelTableComponent::addExtensionClass($class);

        return $this;
    }

    /**
     * @param  string  $name
     * @param  string  $class
     * @return $this
     */
    public function formField(string $name, string $class): static
    {
        Component::registerFormComponent($name, $class);

        return $this;
    }

    /**
     * Get extension scripts.
     * @return array
     */
    public function getScripts(): array
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

    /**
     * Merge scripts to script list
     * @param  array  $scripts
     * @return $this
     */
    public function mergeScripts(array $scripts): static
    {
        $this->scripts = array_merge($this->scripts, $scripts);

        return $this;
    }

    /**
     * Merge styles to style list
     * @param  array  $styles
     * @return $this
     */
    public function mergeStyles(array $styles): static
    {
        $this->styles = array_merge($this->styles, $styles);

        return $this;
    }

    /**
     * @return string
     */
    public function js(): string
    {
        return <<<JS

JS;
    }

    /**
     * @return string
     */
    public function css(): string
    {
        return <<<CSS

CSS;
    }
}
