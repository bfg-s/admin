<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\SliderInput;

/**
 * Input search admin panel to enter the amount.
 */
class SliderSearchInput extends SliderInput
{
    /**
     * Comparisons for the current field.
     *
     * @var string
     */
    public static string $condition = '>=';

    /**
     * Transformation of the input value for search.
     *
     * @param $value
     * @return array
     */
    public static function transformValue($value): array
    {
        return explode(',', $value);
    }
}
