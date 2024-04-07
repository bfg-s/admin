<?php

declare(strict_types=1);

namespace Admin\Components;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Admin\Explanation;
use Admin\Traits\Delegable;
use Admin\Traits\TypesTrait;

class TableComponent extends Component
{
    use TypesTrait;
    use Delegable;

    /**
     * @var string
     */
    protected string $view = 'table';

    /**
     * @var array
     */
    protected array $array_build = [];

    /**
     * @var bool
     */
    protected bool $auto_tbody = false;

    /**
     * @var bool
     */
    protected bool $first_th = true;

    /**
     * @var mixed
     */
    protected mixed $map = null;

    /**
     * @var mixed
     */
    protected mixed $mapWithKeys = null;

    /**
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        $this->type = null;

        parent::__construct();

        $this->explainForce(Explanation::new($delegates));
    }

    /**
     * @return array
     */
    protected function viewData(): array
    {
        $rows = $this->array_build['rows'] ?? $this->array_build;

        if ($this->map) {
            $rows = collect($rows)->map($this->map)->toArray();
        }

        if ($this->mapWithKeys) {
            $rows = collect($rows)->mapWithKeys($this->mapWithKeys)->toArray();
        }

        return [
            'rows' => $rows,
            'array_build' => $this->array_build,
            'type' => $this->type,
            'hasHeader' => isset($this->array_build['headers']) && $this->array_build['rows'],
            'first_th' => $this->first_th,
        ];
    }

    /**
     * @param $rows
     * @return $this
     */
    public function rows($rows): static
    {
        if (is_callable($rows)) {
            $rows = call_user_func($rows);
        }

        if ($rows instanceof Arrayable) {
            $rows = $rows->toArray();
        }

        if (is_array($rows)) {
            $this->array_build = $rows;
        }

        return $this;
    }

    /**
     * @param  Closure  $call
     * @return $this
     */
    public function map(Closure $call): static
    {
        $this->map = $call;

        return $this;
    }

    /**
     * @param  Closure  $call
     * @return $this
     */
    public function mapWithKeys(Closure $call): static
    {
        $this->mapWithKeys = $call;

        return $this;
    }

    /**
     * @return void
     */
    protected function mount(): void
    {

    }
}
