<?php

namespace LteAdmin\Components\SearchFields;

use Carbon\Carbon;
use LteAdmin\Components\Fields\DateRangeField;

class DateRangeSearchField extends DateRangeField
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
            Carbon::create($value[0])->startOfDay(),
            Carbon::create($value[1])->endOfDay(),
        ];
    }
}
