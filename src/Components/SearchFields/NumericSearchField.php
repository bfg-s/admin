<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\NumericInput;

/**
 * Admin panel search input to enter a floating point number.
 */
class NumericSearchField extends NumericInput
{
    /**
     * Comparisons for the current field.
     *
     * @var string
     */
    public static string $condition = '=';
}
