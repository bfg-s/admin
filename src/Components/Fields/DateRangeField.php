<?php

namespace LteAdmin\Components\Fields;

use LteAdmin\Traits\DateControlTrait;

class DateRangeField extends InputField
{
    use DateControlTrait;

    /**
     * @var string
     */
    protected $icon = 'fas fa-calendar';

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'picker::daterange',
    ];

    /**
     * @var array
     */
    protected $params = [
        ['autocomplete' => 'off'],
    ];
}
