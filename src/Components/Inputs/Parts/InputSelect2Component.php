<?php

declare(strict_types=1);

namespace Admin\Components\Inputs\Parts;

use Admin\Components\Component;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;

/**
 * Input admin panel for select2 components.
 */
class InputSelect2Component extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'inputs.parts.input-select2';

    /**
     * Prepared options for select2 components.
     *
     * @var array
     */
    protected mixed $options = [];

    /**
     * Options for select2 components.
     *
     * @var array
     */
    protected array $optionsPrint = [];

    /**
     * The current value of select2 components.
     *
     * @var mixed|null
     */
    protected mixed $value = null;

    /**
     * The current name of select2 components.
     *
     * @var mixed|null
     */
    protected mixed $name = null;

    /**
     * Current select2 component ID.
     *
     * @var mixed|null
     */
    protected mixed $id = null;

    /**
     * Does select2 component have an error.
     *
     * @var mixed|null
     */
    protected mixed $hasBug = null;

    /**
     * Multi Input for select 2 components.
     *
     * @var mixed|null
     */
    protected mixed $multiple = null;

    /**
     * InputSelect2Component constructor.
     *
     * @param $options
     * @param ...$delegates
     */
    public function __construct($options = [], ...$delegates)
    {
        parent::__construct($delegates);

        $this->options = $options;
    }

    /**
     * Set the name select 2 components.
     *
     * @param  mixed  $name
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set ID select 2 components.
     *
     * @param $id
     * @return $this
     */
    public function setId($id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Determine whether the select error has 2 components.
     *
     * @param  mixed  $hasBug
     * @return $this
     */
    public function setHasBug(mixed $hasBug): static
    {
        $this->hasBug = $hasBug;

        return $this;
    }

    /**
     * Set multi input for select 2 components.
     *
     * @param  mixed  $multiple
     * @return $this
     */
    public function setMultiple(mixed $multiple): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Set the value for select 2 components.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setValues(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Create options from prepared options.
     *
     * @return $this
     */
    public function makeOptions(): static
    {
        $this->options($this->options, $this->value);

        return $this;
    }

    /**
     * Add options from array.
     *
     * @param  array  $data
     * @param  null  $select
     * @return $this
     */
    public function options(array $data = [], $select = null): static
    {
        if ($select instanceof Collection) {
            $select = $select->pluck('id');
        }

        if ($select instanceof Arrayable) {
            $select = $select->toArray();
        }

        foreach ($data as $key => $val) {
            $this->optionsPrint[(string) $key] = $val;

            if (is_string($key) || is_numeric($key)) {
                if (is_array($select) && (in_array($key, $select))) {
                    $this->optionsPrint[(string) $key] = [$this->optionsPrint[(string) $key]];
                } elseif ($this->paramEq($select) === $this->paramEq($key)) {
                    $this->optionsPrint[(string) $key] = [$this->optionsPrint[(string) $key]];
                } elseif ($this->paramEq($this->value) === $this->paramEq($key)) {
                    $this->optionsPrint[(string) $key] = [$this->optionsPrint[(string) $key]];
                }
            }
        }

        return $this;
    }

    /**
     * Assistant function for comparing the necessary data.
     *
     * @param $value
     * @return string
     */
    protected function paramEq($value): string
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        return (string) $value;
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'options' => $this->optionsPrint,
            'name' => $this->name,
            'id' => $this->id,
            'hasBug' => $this->hasBug,
            'multiple' => $this->multiple,
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
