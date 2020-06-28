<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

/**
 * Class Amount
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Amount extends Input
{
    /**
     * @var string
     */
    protected $icon = "fas fa-dollar-sign";

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'mask',
        'load-params' => '9{0,}.9{0,}'
    ];
}