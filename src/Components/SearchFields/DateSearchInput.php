<?php

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\DateInput;
use Carbon\Carbon;

class DateSearchInput extends DateInput
{
    /**
     * @var string
     */
    public static string $condition = '>=';

    /**
     * @param $value
     * @return Carbon
     */
    public static function transformValue($value): Carbon
    {
        return Carbon::create($value);
    }
}
