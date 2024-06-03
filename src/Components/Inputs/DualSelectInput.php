<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

/**
 * Input admin panel for dual selection.
 */
class DualSelectInput extends SelectInput
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = null;

    /**
     * The CSS class that needs to be applied to the parent element.
     *
     * @var string|null
     */
    protected string|null $class = 'duallistbox';

    /**
     * Settable date attributes.
     *
     * @var string[]
     */
    protected array $data = [
        'load' => 'duallist',
    ];

    /**
     * Input the admin panel with multi-selection.
     *
     * @var bool
     */
    protected bool $multiple = true;
}
