<?php

namespace Admin\Components\Inputs;

use Admin\Components\Inputs\Parts\InputRadioBox;

class RadiosInput extends ChecksInput
{
    protected array $attributes = ['a' => 1];
    /**
     * @return mixed
     */
    public function field(): mixed
    {
        return InputRadioBox::create($this->options)
            ->name($this->name)
            ->id($this->field_id)
            ->setDatas($this->data)
            ->setRules($this->rules)
            ->value($this->value)
            ->setAttributes($this->attributes);
    }
}
