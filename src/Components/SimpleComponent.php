<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Traits\Delegable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;

/**
 * Abstraction of a simple admin panel page component.
 */
abstract class SimpleComponent implements Renderable, Htmlable
{
    use Delegable;

    /**
     * Name of the admin panel theme template.
     *
     * @var string|null
     */
    protected ?string $template = null;

    /**
     * States of a simple component.
     *
     * @var array
     */
    protected array $state = [];

    /**
     * Simple component delegations.
     *
     * @var array
     */
    protected array $delegates = [];

    /**
     * SimpleComponent constructor.
     *
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        $this->delegates = $delegates;
    }

    /**
     * Converter components to HTML.
     *
     * @return mixed
     */
    public function toHtml(): mixed
    {
        return $this->render();
    }

    /**
     * Render the component.
     *
     * @return mixed
     */
    public function render(): mixed
    {
        $this->newExplain($this->delegates);

        return $this->template
            ? view("admin::theme.$this->template", $this->state)
            : "";
    }

    /**
     * Magic method for getting the state property of a component.
     *
     * @param  string  $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->state[$name];
    }

    /**
     * Magic method for setting the state property of a component.
     *
     * @param  string  $name
     * @param $value
     * @return void
     */
    public function __set(string $name, $value): void
    {
        $this->state[$name] = $value;
    }

    /**
     * Method for creating a new component instance.
     *
     * @param ...$delegates
     * @return $this
     */
    public function new(...$delegates): static
    {
        return new static(...$delegates);
    }
}
