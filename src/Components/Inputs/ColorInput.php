<?php

namespace Admin\Components\Inputs;

class ColorInput extends Input
{
    /**
     * @var string|null
     */
    protected ?string $icon = 'fas fa-fill-drip';

    /**
     * @var string[]
     */
    protected array $data = [
        'load' => 'picker::color',
    ];

    /**
     * @return string
     */
    protected function app_end_field(): string
    {
        return "<span class='input-group-append'>
                <span class='input-group-text'><i class='fas fa-square' style='color: {$this->value}'></i></span>
            </span>";
    }
}
