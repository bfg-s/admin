<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Traits\DateControlTrait;

class DateTimeRangeInput extends DateRangeInput
{
    use DateControlTrait;

    /**
     * @var string[]
     */
    protected array $data = [
        'load' => 'picker::datetimerange',
    ];
}
