<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\DateTimeRangeInput;
use Carbon\Carbon;

class DateTimeRangeSearchField extends DateTimeRangeInput
{
    /**
     * @var string
     */
    public static string $condition = 'between';

    /**
     * @param $value
     * @return array
     */
    public static function transformValue($value)
    {
        $value = explode(' - ', $value);

        return [
            Carbon::create($value[0]),
            Carbon::create($value[1]),
        ];
    }
}
