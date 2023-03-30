<?php

namespace Admin\Components\Fields;

use Admin\Traits\DateControlTrait;

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
