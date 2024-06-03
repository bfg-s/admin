<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

/**
 * Input the admin panel to select an icon.
 */
class IconInput extends Input
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = 'fas fa-icons';

    /**
     * Data that needs to be placed after the input.
     *
     * @return string
     */
    protected function app_end_field(): string
    {
        return "<span class='input-group-append'>
                <button class='btn btn-primary' data-icon='{$this->value}' data-load='picker::icon'></button>
            </span>";
    }
}
