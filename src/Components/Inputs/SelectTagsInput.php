<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\Inputs\Parts\InputSelect2TagsComponent;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Input admin panel for selecting tags from a drop-down list.
 */
class SelectTagsInput extends SelectInput
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = 'fas fa-tags';

    /**
     * Method for creating an input field.
     *
     * @return mixed
     */
    public function field(): mixed
    {
        return InputSelect2TagsComponent::create($this->options)
            ->setName($this->name)
            ->setId($this->field_id)
            ->setValues($this->value)
            ->setHasBug($this->has_bug)
            ->makeOptions()
            ->setDatas($this->data)
            ->addClass($this->class);
    }

    /**
     * Add options to the current input.
     *
     * @param  array|Arrayable  $options
     * @param  bool  $first_default
     * @return SelectTagsInput
     */
    public function options(array|Arrayable $options, bool $first_default = false): static
    {
        return parent::options($options, $this->load_subject ? false : $first_default);
    }
}
