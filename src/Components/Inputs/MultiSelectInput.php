<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Input admin panel for multi-selection of data.
 */
class MultiSelectInput extends SelectInput
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected string|null $icon = null;

    /**
     * Input the admin panel with multi-selection.
     *
     * @var bool
     */
    protected bool $multiple = true;

    /**
     * Add options to the current input.
     *
     * @param  array|Arrayable  $options
     * @param  bool  $first_default
     * @return MultiSelectInput
     */
    public function options(array|Arrayable $options, bool $first_default = false): static
    {
        return parent::options($options, $this->load_subject ? false : $first_default);
    }
}
