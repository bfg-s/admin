<?php

namespace LteAdmin\Components;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use LteAdmin\Traits\Delegable;

abstract class SimpleComponent implements Renderable, Htmlable
{
    use Delegable;

    protected ?string $template = null;

    protected array $state = [];

    protected array $delegates = [];

    /**
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        $this->delegates = $delegates;
    }

    public function toHtml()
    {
        return $this->render();
    }

    public function render()
    {
        $this->newExplain($this->delegates);

        return $this->template
            ? view("lte::theme.$this->template", $this->state)
            : "";
    }

    public function __get(string $name)
    {
        return $this->state[$name];
    }

    public function __set(string $name, $value): void
    {
        $this->state[$name] = $value;
    }

    public function new(...$delegates)
    {
        return new static(...$delegates);
    }
}
