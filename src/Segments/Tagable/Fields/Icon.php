<?php

namespace Lar\LteAdmin\Segments\Tagable\Fields;

/**
 * Class Icon
 * @package Lar\LteAdmin\Segments\Tagable\Fields
 */
class Icon extends Input
{
    /**
     * @var string
     */
    protected $icon = "fas fa-icons";

    /**
     * @return string
     */
    protected function app_end_field()
    {
        return "<span class='input-group-append'>
                <button class='btn btn-primary' data-icon='{$this->value}' data-load='picker::icon'></button>
            </span>";
    }
}