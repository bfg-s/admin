<?php

namespace Admin\Components\Fields;

use Admin\Traits\DateControlTrait;

class TimeField extends InputField
{
    use DateControlTrait;

    /**
     * @var string
     */
    protected $icon = 'fas fa-clock';

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'picker::time',
        'toggle' => 'datetimepicker',
    ];

    /**
     * @var array
     */
    protected $params = [
        ['autocomplete' => 'off'],
    ];

    /**
     * On build field.
     */
    protected function on_build()
    {
        $this->data['target'] = "#{$this->field_id}";
    }
}
