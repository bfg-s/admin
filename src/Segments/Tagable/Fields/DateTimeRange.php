<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

/**
 * Class DateRange
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class DateTimeRange extends DateRange
{

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'picker::datetimerange'
    ];
}