<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\SwitcherInput;

/**
 * Input search admin panel switch.
 */
class SwitcherSearchField extends SwitcherInput
{
    /**
     * Comparisons for the current field.
     *
     * @var string
     */
    public static string $condition = '=';
}
