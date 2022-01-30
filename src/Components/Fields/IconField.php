<?php

namespace LteAdmin\Components\Fields;

class IconField extends InputField
{
    /**
     * @var string
     */
    protected $icon = 'fas fa-icons';

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
