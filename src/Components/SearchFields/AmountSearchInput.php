<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\AmountInput;

/**
 * Input search admin panel to enter the amount.
 */
class AmountSearchInput extends AmountInput
{
    /**
     * Comparisons for the current field.
     *
     * @var string
     */
    public static string $condition = '>=';
}
