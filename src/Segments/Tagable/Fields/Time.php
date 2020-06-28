<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

use Lar\LteAdmin\Segments\Tagable\Traits\DateControlTrait;

/**
 * Class Time
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Time extends Input
{
    use DateControlTrait;

    /**
     * @var string
     */
    protected $icon = "fas fa-clock";

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'picker::time',
        'toggle' => 'datetimepicker'
    ];

    /**
     * On build field
     */
    protected function on_build()
    {
        $this->data['target'] = "#{$this->field_id}";
    }
}