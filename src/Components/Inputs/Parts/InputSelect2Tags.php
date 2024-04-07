<?php

declare(strict_types=1);

namespace Admin\Components\Inputs\Parts;

use Admin\Components\Component;

class InputSelect2Tags extends Component
{
    protected string $view = 'inputs.parts.input-select2';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected array $optionsPrint = [];

    /**
     * @var mixed|null
     */
    protected $value;

    protected $name;

    protected $id;

    protected $hasBug;

    protected array $datas = [
        'tags' => 'true'
    ];

    public function __construct($options, ...$delegates)
    {
        parent::__construct($delegates);
        $this->options = $options;
    }

    public function setDatas(array $datas): static
    {
        $this->datas = array_merge($this->datas, $datas);

        return $this;
    }

    protected function viewData(): array
    {
        return [
            'options' => $this->optionsPrint,
            'name' => $this->name,
            'id' => $this->id,
            'hasBug' => $this->hasBug,
            'datas' => $this->datas,
            'multiple' => true,
        ];
    }

    /**
     * @return $this
     */
    public function makeOptions()
    {
        if (is_array($this->value)) {
            foreach ($this->value as $item) {
                $key = array_search($item, $this->options);
                if ($key !== false) {
                    unset($this->options[$key]);
                }
                $this->optionsPrint[(string) $item] = [$item];
            }
        }

        foreach ($this->options as $option) {
            $this->optionsPrint[(string) $option] = $option;
        }

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

    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
