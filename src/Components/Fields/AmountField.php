<?php

namespace LteAdmin\Components\Fields;

class AmountField extends InputField
{
    /**
     * @var string
     */
    protected $icon = 'fas fa-dollar-sign';

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'mask',
        'load-params' => '9{0,}.9{0,}',
    ];
}
