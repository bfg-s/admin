<?php

namespace LteAdmin\Components\SearchFields;

use Carbon\Carbon;
use LteAdmin\Components\Fields\DateTimeRangeField;

class DateTimeRangeSearchField extends DateTimeRangeField
{
    /**
     * @var string
     */
    public static $condition = 'between';

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
