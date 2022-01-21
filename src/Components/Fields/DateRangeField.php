<?php

namespace Lar\LteAdmin\Components\Fields;

use Lar\LteAdmin\Components\Traits\DateControlTrait;

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
