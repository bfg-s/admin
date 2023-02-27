<?php

namespace LteAdmin\Components\Fields;

class NumericField extends InputField
{
    /**
     * @var string
     */
    protected $icon = 'fas fa-hashtag';

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'mask',
        'load-params' => '-{0,1}9{0,}',
    ];
}
