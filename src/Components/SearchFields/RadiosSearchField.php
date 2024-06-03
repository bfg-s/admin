<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\RadiosInput;

/**
 * Search input for the admin panel to select a radio button.
 */
class RadiosSearchField extends RadiosInput
{
    /**
     * Comparisons for the current field.
     *
     * @var string
     */
    public static string $condition = '=';
}
