<?php

namespace LteAdmin\Components\Fields;

use LteAdmin\Traits\DateControlTrait;

class DateTimeRangeField extends DateRangeField
{
    use DateControlTrait;

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'picker::datetimerange',
    ];
}
