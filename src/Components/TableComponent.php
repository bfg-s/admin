<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Explanation;
use Admin\Traits\Typeable;
use Closure;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Table component of the admin panel.
 */
class TableComponent extends Component
{
    use Typeable;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'table';

    /**
     * Array to construct the table.
     *
     * @var array
     */
    protected array $array_build = [];

    /**
     * Add a "row" scope to the first "th" of the table.
     *
     * @var bool
     */
    protected bool $first_th = true;

    /**
     * Add a callback for mapping table rows.
     *
     * @var mixed
     */
    protected mixed $map = null;

    /**
     * Add a callback for mapping with table row keys.
     *
     * @var mixed
     */
    protected mixed $mapWithKeys = null;

    /**
     * Realtime marker, if enabled, the component will be updated at the specified frequency.
     *
     * @var bool
     */
    protected bool $realTime = true;

    /**
     * TableComponent constructor.
     *
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        $this->type = null;

        parent::__construct();

        $this->explainForce(Explanation::new($delegates));
    }

    /**
     * Add rows to the table.
     *
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
     * Add a callback for mapping table rows.
     *
     * @param  Closure  $call
     * @return $this
     */
    public function map(Closure $call): static
    {
        $this->map = $call;

        return $this;
    }

    /**
     * Add a callback for mapping with table row keys.
     *
     * @param  Closure  $call
     * @return $this
     */
    public function mapWithKeys(Closure $call): static
    {
        $this->mapWithKeys = $call;

        return $this;
    }

    /**
     * Additional data to be sent to the template.
     *
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
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
    }
}
