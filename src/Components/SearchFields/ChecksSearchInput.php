<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\ChecksInput;

/**
 * Search input for the admin panel for checkboxes.
 */
class ChecksSearchInput extends ChecksInput
{
    /**
     * Comparisons for the current field.
     *
     * @var string
     */
    public static string $condition = 'in';
}
