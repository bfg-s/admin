<?php

namespace Admin\Components\Inputs;

class AmountInput extends Input
{
    /**
     * @var string|null
     */
    protected ?string $icon = 'fas fa-dollar-sign';

    /**
     * @var string[]
     */
    protected array $data = [
        'load' => 'mask',
        'load-params' => '9{0,}.9{0,}',
    ];
}
