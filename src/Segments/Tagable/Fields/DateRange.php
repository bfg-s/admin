<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

/**
 * Class DateRange
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class DateRange extends Input
{
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
}