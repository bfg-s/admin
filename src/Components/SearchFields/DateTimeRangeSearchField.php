<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\DateTimeRangeInput;
use Carbon\Carbon;

/**
 * Search input to the admin panel to select a date and time range.
 */
class DateTimeRangeSearchField extends DateTimeRangeInput
{
    /**
     * Comparisons for the current field.
     *
     * @var string
     */
    public static string $condition = 'between';

    /**
     * Transformation of the input value for search.
     *
     * @param $value
     * @return array
     */
    public static function transformValue($value): array
    {
        $value = explode(' - ', $value);

        return [
            Carbon::create($value[0]),
            Carbon::create($value[1]),
        ];
    }
}
