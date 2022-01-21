<?php

namespace Lar\LteAdmin\Components\Fields;

use Lar\LteAdmin\Components\Traits\DateControlTrait;

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
