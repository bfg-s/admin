<?php

declare(strict_types=1);

namespace Admin\Themes;

/**
 * Abstract visual theme class.
 */
abstract class Theme
{
    /**
     * Name of the visual theme.
     *
     * @var string
     */
    protected string $name;

    /**
     * Description of the visual theme.
     *
     * @var string
     */
    protected string $description;

    /**
     * Blade render variable to form the template path.
     *
     * @var string
     */
    protected string $viewVariable;

    /**
     * Template namespace to form a template group.
     *
     * @var string|null
     */
    protected ?string $namespace = null;

    /**
     * Path to the templates of theme directory.
     *
     * @var string|null
     */
    protected string|null $directory = null;

    /**
     * Visual theme scripts.
     *
     * @var array
     */
    protected array $scripts = [];

    /**
     * Initial visual theme scripts.
     *
     * @var array
     */
    protected array $firstScripts = [];

    /**
     * Visual theme styles.
     *
     * @var array
     */
    protected array $styles = [];

    /**
     * Visual theme slug.
     *
     * @var string
     */
    protected string $slug;

    /**
     * Visual theme meta tags.
     *
     * @return array
     */
    public function metas(): array
    {
        return [];
    }

    /**
     * Get the name of the visual theme.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get a description of the visual theme.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the visual theme directory.
     *
     * @return string|null
     */
    public function getDirectory(): ?string
    {
        return $this->directory;
    }

    /**
     * Get the namespace of the visual theme.
     *
     * @return string|null
     */
    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    /**
     * Get visual theme scripts.
     *
     * @return array
     */
    public function getScripts(): array
    {
        return $this->scripts;
    }

    /**
     * Get visual theme styles.
     *
     * @return array
     */
    public function getStyles(): array
    {
        return $this->styles;
    }

    /**
     * Get a visual theme slug.
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Generate the path to the visual theme template.
     *
     * @param  string  $path
     * @return string
     */
    public function template(string $path): string
    {
        return $this->getViewVariable().$path;
    }

    /**
     * Get the visual theme view variable.
     *
     * @return string
     */
    public function getViewVariable(): string
    {
        return $this->viewVariable;
    }

    /**
     * Get the visual theme javascript.
     *
     * @return string
     */
    public function js(): string
    {
        return <<<JS

JS;
    }

    /**
     * Get the CSS of the visual theme.
     *
     * @return string
     */
    public function css(): string
    {
        return <<<CSS

CSS;
    }

    /**
     * Get initial visual theme scripts.
     *
     * @return array
     */
    public function getFirstScripts(): array
    {
        return $this->firstScripts;
    }
}
