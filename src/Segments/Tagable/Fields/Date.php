<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;


/**
 * Class Email
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Date extends Input
{
    /**
     * @var string
     */
    protected $icon = "fas fa-calendar-plus";

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'picker::date',
        'toggle' => 'datetimepicker'
    ];

    /**
     * @param  string  $name
     * @param  string  $title
     * @param  string  $id
     * @param  null  $value
     * @param  bool  $has_bug
     * @param  null  $path
     * @return \Lar\Layout\Abstracts\Component|\Lar\Layout\Tags\INPUT|mixed
     */
    public function field(string $name, string $title, string $id = '', $value = null, bool $has_bug = false, $path = null)
    {
        $this->data['target'] = "#{$id}";

        return parent::field($name, $title, $id, $value, $has_bug, $path);
    }
}