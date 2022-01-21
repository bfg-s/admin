<?php

namespace Lar\LteAdmin\Components\Fields;

class ColorField extends InputField
{
    /**
     * @var string
     */
    protected $icon = 'fas fa-fill-drip';

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'picker::color',
    ];

    /**
     * @return string
     */
    protected function app_end_field()
    {
        return "<span class='input-group-append'>
                <span class='input-group-text'><i class='fas fa-square' style='color: {$this->value}'></i></span>
            </span>";
    }
}
