<?php

namespace Admin\Components\Fields;

use Admin\Traits\DateControlTrait;

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
