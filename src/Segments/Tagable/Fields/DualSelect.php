<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

/**
 * Class DualSelect
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class DualSelect extends Select
{
    /**
     * @var string
     */
    protected $icon = null;

    /**
     * @var string
     */
    protected $class = "form-control duallistbox";

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'duallist'
    ];

    /**
     * @var array
     */
    protected $params = [
        ['multiple' => 'multiple']
    ];
}