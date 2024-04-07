<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\DateTimeInput;
use Carbon\Carbon;

class DateTimeSearchInput extends DateTimeInput
{
    /**
     * @var string
     */
    public static string $condition = '>=';

    /**
     * @param $value
     * @return Carbon
     */
    public static function transformValue($value)
    {
        return Carbon::create($value);
    }
}
