<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\NumberInput;

/**
 * Search input to the admin panel to enter a number.
 */
class NumberSearchField extends NumberInput
{
    /**
     * Comparisons for the current field.
     *
     * @var string
     */
    public static string $condition = '=';
}
