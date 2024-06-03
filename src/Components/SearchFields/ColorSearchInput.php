<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\ColorInput;

/**
 * Search input for the admin panel to select a color.
 */
class ColorSearchInput extends ColorInput
{
    /**
     * Comparisons for the current field.
     *
     * @var string
     */
    public static string $condition = '=';
}
