<?php

namespace Admin\Components\Inputs;

use Admin\Traits\DateControlTrait;

class DateRangeInput extends Input
{
    use DateControlTrait;

    /**
     * @var string|null
     */
    protected ?string $icon = 'fas fa-calendar';

    /**
     * @var string[]
     */
    protected array $data = [
        'load' => 'picker::daterange',
    ];

    /**
     * @var string
     */
    protected string $autocomplete = 'off';
}
