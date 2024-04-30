<?php

declare(strict_types=1);

namespace Admin\Components\Inputs\Parts;

use Admin\Components\Component;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;

class InputSelect2 extends Component
{
    /**
     * @var string
     */
    protected string $view = 'inputs.parts.input-select2';

    /**
     * @var array
     */
    protected mixed $options = [];

    /**
     * @var array
     */
    protected array $optionsPrint = [];

    /**
     * @var mixed|null
     */
    protected mixed $value = null;

    /**
     * @var mixed|null
     */
    protected mixed $name = null;

    /**
     * @var mixed|null
     */
    protected mixed $id = null;

    /**
     * @var mixed|null
     */
    protected mixed $hasBug = null;

    /**
     * @var mixed|null
     */
    protected mixed $multiple = null;

    /**
     * @param $options
     * @param ...$delegates
     */
    public function __construct($options = [], ...$delegates)
    {
        parent::__construct($delegates);

        $this->options = $options;
    }

    /**
     * @param  mixed  $name
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param  mixed  $id
     */
    public function setId($id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param  mixed  $hasBug
     */
    public function setHasBug($hasBug): static
    {
        $this->hasBug = $hasBug;

        return $this;
    }

    /**
     * @param  mixed  $multiple
     */
    public function setMultiple($multiple): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValues($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function makeOptions()
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
    public function options($data = [], $select = null)
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
     * @return void
     */
    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
