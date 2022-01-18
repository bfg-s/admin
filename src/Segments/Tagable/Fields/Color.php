<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

/**
 * Class Color.
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Color extends Input
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
