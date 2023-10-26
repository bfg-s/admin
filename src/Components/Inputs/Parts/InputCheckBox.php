<?php

namespace Admin\Components\Inputs\Parts;

use Admin\Components\Component;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;

class InputCheckBox extends Component
{
    /**
     * @var string
     */
    protected string $view = 'inputs.parts.input-check-box';

    /**
     * @var array|Arrayable
     */
    protected $values;

    /**
     * @var string|array
     */
    protected $value;

    protected $id;

    protected $name;

    public function __construct($values, ...$delegates)
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        if (!is_array($values)) {
            $values = [$values];
        }

        $this->values = $values;

        parent::__construct($delegates);
    }

    /**
     * @param $id
     * @return $this
     */
    public function id($id)
    {
        if ($id) {
            $this->id = $id;
        }

        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function name($name)
    {
        if ($name) {
            $this->name = $name;
        }

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function value($value)
    {
        if ($value instanceof Collection) {
            $value = $value->pluck('id');
        }

        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        }

        if ($value !== null) {
            $this->value = $value;
        }

        return $this;
    }

    protected function viewData(): array
    {
        return [
            'values' => $this->values,
            'id' => $this->id,
            'val' => $this->value,
            'name' => $this->name,
        ];
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
