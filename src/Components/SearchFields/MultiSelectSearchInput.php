<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\MultiSelectInput;

/**
 * Search input of the admin panel for multi-selection of data.
 */
class MultiSelectSearchInput extends MultiSelectInput
{
    /**
     * Comparisons for the current field.
     *
     * @var string
     */
    public static string $condition = 'in';
}
