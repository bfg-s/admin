<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Traits\DateControlTrait;

/**
 * Input the admin panel to select a date and time range.
 */
class DateTimeRangeInput extends DateRangeInput
{
    use DateControlTrait;

    /**
     * Settable date attributes.
     *
     * @var string[]
     */
    protected array $data = [
        'load' => 'picker::datetimerange',
    ];
}
