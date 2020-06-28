<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Lar\LteAdmin\Segments\Tagable\Traits\DateControlTrait;

/**
 * Class DateTimeRange
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class DateTimeRange extends DateRange
{
    use DateControlTrait;

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'picker::datetimerange'
    ];
}