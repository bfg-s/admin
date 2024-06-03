<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Components\Inputs\Parts\InputRadioBoxComponent;

/**
 * Input admin panel for selecting options from radio buttons.
 */
class RadiosInput extends ChecksInput
{
    /**
     * List of attributes that should be applied to the first HTML element of the component.
     *
     * @var array|int[]
     */
    protected array $attributes = [
        'a' => 1
    ];

    /**
     * Method for creating an input field.
     *
     * @return mixed
     */
    public function field(): mixed
    {
        return InputRadioBoxComponent::create($this->options)
            ->name($this->name)
            ->id($this->field_id)
            ->setDatas($this->data)
            ->setRules($this->rules)
            ->value($this->value)
            ->setAttributes($this->attributes);
    }
}
