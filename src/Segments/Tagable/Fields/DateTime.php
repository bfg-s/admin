<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Lar\LteAdmin\Segments\Tagable\Traits\DateControlTrait;

/**
 * Class DateTime
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class DateTime extends Input
{
    use DateControlTrait;

    /**
     * @var string
     */
    protected $icon = "fas fa-calendar-plus";

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'picker::datetime',
        'toggle' => 'datetimepicker'
    ];

    /**
     * @var array
     */
    protected $params = [
        ['autocomplete' => 'off']
    ];

    /**
     * On build
     */
    protected function on_build()
    {
        $this->data['target'] = "#{$this->field_id}";
    }
}