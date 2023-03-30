<?php

namespace Admin\Components\Fields;

use Admin\Traits\DateControlTrait;

class DateField extends InputField
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
        'load' => 'picker::date',
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
