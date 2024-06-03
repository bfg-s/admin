<?php

declare(strict_types=1);

namespace Admin\Components\Inputs\Parts;

use Admin\Components\Component;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;

/**
 * Input admin panel for checkbox.
 */
class InputCheckBoxComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'inputs.parts.input-check-box';

    /**
     * Values for checkboxes.
     *
     * @var array
     */
    protected array $values;

    /**
     * Current value for checkboxes.
     *
     * @var string|array
     */
    protected mixed $value;

    /**
     * Current checkbox ID.
     *
     * @var string|null
     */
    protected string|null $id = null;

    /**
     * The current name of the checkboxes.
     *
     * @var string|null
     */
    protected string|null $name = null;

    /**
     * InputCheckBox constructor.
     *
     * @param $values
     * @param ...$delegates
     */
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
     * Set checkbox ids.
     *
     * @param $id
     * @return $this
     */
    public function id($id): static
    {
        if ($id) {
            $this->id = $id;
        }

        return $this;
    }

    /**
     * Set the name of the checkboxes.
     *
     * @param $name
     * @return $this
     */
    public function name($name): static
    {
        if ($name) {
            $this->name = $name;
        }

        return $this;
    }

    /**
     * Set the value of the checkboxes.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function value(mixed $value): static
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

    /**
     * Additional data to be sent to the template.
     *
     * @return array
     */
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
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
