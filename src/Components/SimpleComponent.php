<?php

declare(strict_types=1);

namespace Admin\Components;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Admin\Traits\Delegable;

abstract class SimpleComponent implements Renderable, Htmlable
{
    use Delegable;

    /**
     * @var string|null
     */
    protected ?string $template = null;

    /**
     * @var array
     */
    protected array $state = [];

    /**
     * @var array
     */
    protected array $delegates = [];

    /**
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        $this->delegates = $delegates;
    }

    /**
     * @return mixed
     */
    public function toHtml(): mixed
    {
        return $this->render();
    }

    /**
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
     * @param  string  $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->state[$name];
    }

    /**
     * @param  string  $name
     * @param $value
     * @return void
     */
    public function __set(string $name, $value): void
    {
        $this->state[$name] = $value;
    }

    /**
     * @param ...$delegates
     * @return $this
     */
    public function new(...$delegates): static
    {
        return new static(...$delegates);
    }
}
