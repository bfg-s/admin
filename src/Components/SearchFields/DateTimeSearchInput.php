<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\DateTimeInput;
use Carbon\Carbon;

/**
 * Search input for the admin panel to select the date and time.
 */
class DateTimeSearchInput extends DateTimeInput
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
     * @return Carbon
     */
    public static function transformValue($value): Carbon
    {
        return Carbon::create($value);
    }
}
