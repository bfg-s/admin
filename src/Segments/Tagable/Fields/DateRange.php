<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Lar\LteAdmin\Segments\Tagable\Traits\DateControlTrait;

/**
 * Class DateRange
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class DateRange extends Input
{
    use DateControlTrait;

    /**
     * @var string
     */
    protected $icon = "fas fa-calendar";

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'picker::daterange'
    ];

    /**
     * @var array
     */
    protected $params = [
        ['autocomplete' => 'off']
    ];
}