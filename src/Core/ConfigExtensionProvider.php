<?php

declare(strict_types=1);

namespace Admin\Core;

use Admin\Components\Component;
use Admin\Components\ModelTableComponent;
use Admin\Controllers\DashboardController;
use Admin\ExtendProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\RouteRegistrar;

/**
 * Abstract class for extending the configuration of the package.
 * This class is the base class for all configuration classes in the package.
 */
abstract class ConfigExtensionProvider
{
    /**
     * Current extension provider.
     *
     * @var ExtendProvider
     */
    public ExtendProvider $provider;

    /**
     * Global extension scripts.
     *
     * @var array
     */
    protected array $scripts = [];

    /**
     * Global extension styles.
     *
     * @var array
     */
    protected array $styles = [];

    /**
     * ConfigExtensionProvider constructor.
     *
     * @param  ExtendProvider  $provider
     */
    public function __construct(ExtendProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Method for initializing the configuration. Called when the configuration is loaded.
     */
    public function boot()
    {
        //
    }

    /**
     * Register extension routers.
     *
     * @param  RouteRegistrar  $route
     * @return void
     */
    public function routes(RouteRegistrar $route)
    {
        //
    }

    /**
     * Helper property for adding extensions to the model table.
     *
     * @param  string  $name
     * @param  Closure  $call
     * @return $this
     */
    public function registerModelTableExtension(string $name, Closure $call): static
    {
        ModelTableComponent::addExtension($name, $call);

        return $this;
    }

    /**
     * Helper property for adding an extension class to a model table.
     *
     * @param  string  $class
     * @return $this
     */
    public function registerModelTableExtensionClass(string $class): static
    {
        ModelTableComponent::addExtensionClass($class);

        return $this;
    }

    /**
     * Helper property for adding inputs to the form.
     *
     * @param  string  $name
     * @param  string  $class
     * @return $this
     */
    public function registerFormComponent(string $name, string $class): static
    {
        Component::registerFormComponent($name, $class);

        return $this;
    }

    /**
     * Helper property for registering a global component.
     *
     * @param  string  $name
     * @param  string  $class
     * @return $this
     */
    public function registerComponent(string $name, string $class): static
    {
        Component::registerComponent($name, $class);

        return $this;
    }

    /**
     * Helper property for registration new dashboard widget.
     *
     * @param  string  $class
     * @return $this
     */
    public function registerDashboardWidget(string $class): static
    {
        DashboardController::addWidget($class);

        return $this;
    }

    /**
     * Get extension scripts.
     *
     * @return array
     */
    public function getScripts(): array
    {
        return $this->scripts;
    }

    /**
     * Get extension styles.
     *
     * @return array
     */
    public function getStyles(): array
    {
        return $this->styles;
    }

    /**
     * Merge scripts to extension script list.
     *
     * @param  array  $scripts
     * @return $this
     */
    public function mergeScripts(array $scripts): static
    {
        $this->scripts = array_merge($this->scripts, $scripts);

        return $this;
    }

    /**
     * Merge styles to extension style list.
     *
     * @param  array  $styles
     * @return $this
     */
    public function mergeStyles(array $styles): static
    {
        $this->styles = array_merge($this->styles, $styles);

        return $this;
    }

    /**
     * Method for adding meta tags to the admin panel.
     *
     * @return array
     */
    public function metas(): array
    {
        return [];
    }

    /**
     * Method for adding JavaScript scripts to the admin panel.
     *
     * @return string
     */
    public function js(): string
    {
        return <<<JS

JS;
    }

    /**
     * Method for adding CSS styles to the admin panel.
     *
     * @return string
     */
    public function css(): string
    {
        return <<<CSS

CSS;
    }

    /**
     * Register middleware callback.
     * To process the current request of the panel page at the middleware level.
     *
     * @param  Request  $request
     * @return void
     */
    public function middleware(Request $request): void
    {
        //
    }

    /**
     * Register response callback.
     * To process the current response of the panel page.
     *
     * @param  Response  $response
     * @return Response
     */
    public function response(Response $response): Response
    {
        return $response;
    }
}
