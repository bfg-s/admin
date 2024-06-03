<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Traits\DateControlTrait;

/**
 * Input the admin panel to select a date range.
 */
class DateRangeInput extends Input
{
    use DateControlTrait;

    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = 'fas fa-calendar';

    /**
     * Settable date attributes.
     *
     * @var string[]
     */
    protected array $data = [
        'load' => 'picker::daterange',
    ];

    /**
     * The autocomplete attribute of component.
     *
     * @var string
     */
    protected string $autocomplete = 'off';
}
