<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

/**
 * Class Icon
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Numeric extends Input
{
    /**
     * @var string
     */
    protected $icon = "fas fa-hashtag";

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'mask',
        'load-params' => '9{0,}'
    ];
}