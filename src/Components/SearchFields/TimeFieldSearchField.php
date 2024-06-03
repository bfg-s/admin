<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\TimeInput;

/**
 * Input search admin panel to enter time.
 */
class TimeFieldSearchField extends TimeInput
{
    /**
     * Comparisons for the current field.
     *
     * @var string
     */
    public static string $condition = '=';
}
