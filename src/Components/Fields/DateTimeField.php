<?php

namespace Lar\LteAdmin\Components\Fields;

use Lar\LteAdmin\Components\Traits\DateControlTrait;

class DateTimeField extends InputField
{
    use DateControlTrait;

    /**
     * @var string
     */
    protected $icon = 'fas fa-calendar-plus';

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'picker::datetime',
        'toggle' => 'datetimepicker',
    ];

    /**
     * @var array
     */
    protected $params = [
        ['autocomplete' => 'off'],
    ];

    /**
     * On build.
     */
    protected function on_build()
    {
        $this->data['target'] = "#{$this->field_id}";
    }
}
