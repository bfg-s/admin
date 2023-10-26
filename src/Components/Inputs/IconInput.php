<?php

namespace Admin\Components\Inputs;

class IconInput extends Input
{
    /**
     * @var string|null
     */
    protected ?string $icon = 'fas fa-icons';

    /**
     * @return string
     */
    protected function app_end_field(): string
    {
        return "<span class='input-group-append'>
                <button class='btn btn-primary' data-icon='{$this->value}' data-load='picker::icon'></button>
            </span>";
    }
}
