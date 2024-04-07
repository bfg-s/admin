<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\Inputs\Parts\InputSelect2Tags;
use Illuminate\Contracts\Support\Arrayable;

class SelectTagsInput extends SelectInput
{
    /**
     * @var string|null
     */
    protected ?string $icon = 'fas fa-tags';

    /**
     * @return mixed
     */
    public function field(): mixed
    {
        return InputSelect2Tags::create($this->options)
            ->setName($this->name)
            ->setId($this->field_id)
            ->setValues($this->value)
            ->setHasBug($this->has_bug)
            ->makeOptions()
            ->setDatas($this->data)
            ->addClass($this->class);
    }

    /**
     * @param  array|Arrayable  $options
     * @param  bool  $first_default
     * @return SelectTagsInput
     */
    public function options(array|Arrayable $options, bool $first_default = false): static
    {
        return parent::options($options, $this->load_subject ? false : $first_default);
    }
}
