<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

/**
 * Input the admin panel to select a color.
 */
class ColorInput extends Input
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = 'fas fa-fill-drip';

    /**
     * Settable date attributes.
     *
     * @var string[]
     */
    protected array $data = [
        'load' => 'picker::color',
    ];

    /**
     * Data that needs to be placed after the input.
     *
     * @return string
     */
    protected function app_end_field(): string
    {
        return "<span class='input-group-append'>
                <span class='input-group-text'><i class='fas fa-square' style='color: {$this->value}'></i></span>
            </span>";
    }
}
