<?php

namespace Admin\Components\Inputs;

use Illuminate\Contracts\Support\Arrayable;

class MultiSelectInput extends SelectInput
{
    /**
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * @var bool
     */
    protected bool $multiple = true;

    /**
     * @param  array|Arrayable  $options
     * @param  bool  $first_default
     * @return MultiSelectInput
     */
    public function options(array|Arrayable $options, bool $first_default = false): static
    {
        return parent::options($options, $this->load_subject ? false : $first_default);
    }
}
