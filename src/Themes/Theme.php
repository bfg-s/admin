<?php

namespace Admin\Themes;

abstract class Theme
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $description;

    /**
     * @var string
     */
    protected string $viewVariable;

    /**
     * @var string|null
     */
    protected ?string $namespace = null;

    /**
     * @var string|null
     */
    protected ?string $directory = null;

    /**
     * @var array
     */
    protected array $scripts = [];

    /**
     * @var array
     */
    protected array $firstScripts = [];

    /**
     * @var array
     */
    protected array $styles = [];

    /**
     * @var string
     */
    protected string $slug;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getViewVariable(): string
    {
        return $this->viewVariable;
    }

    /**
     * @return string|null
     */
    public function getDirectory(): ?string
    {
        return $this->directory;
    }

    /**
     * @return string|null
     */
    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    /**
     * @return array
     */
    public function getScripts(): array
    {
        return $this->scripts;
    }

    /**
     * @return array
     */
    public function getStyles(): array
    {
        return $this->styles;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param  string  $path
     * @return string
     */
    public function template(string $path): string
    {
        return $this->getViewVariable() . $path;
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

    /**
     * @return array
     */
    public function getFirstScripts(): array
    {
        return $this->firstScripts;
    }
}
