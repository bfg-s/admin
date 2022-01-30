<?php

namespace LteAdmin\Components\Fields;

class DualSelectField extends SelectField
{
    /**
     * @var string
     */
    protected $icon = null;

    /**
     * @var string
     */
    protected $class = 'form-control duallistbox';

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'duallist',
    ];

    /**
     * @var array
     */
    protected $params = [
        ['multiple' => 'multiple'],
    ];
}
