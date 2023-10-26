<?php

namespace Admin\Components\Inputs;

class DualSelectInput extends SelectInput
{
    /**
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * @var string
     */
    protected $class = 'duallistbox';

    /**
     * @var string[]
     */
    protected array $data = [
        'load' => 'duallist',
    ];

    /**
     * @var bool
     */
    protected bool $multiple = true;

    /**
     * @param  string[]  $data
     * @return DualSelectInput
     */
    public function setData(array $data): DualSelectInput
    {
        $this->data = $data;
        return $this;
    }
}
