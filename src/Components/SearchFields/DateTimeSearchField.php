<?php

namespace Lar\LteAdmin\Components\SearchFields;

use Carbon\Carbon;
use Lar\LteAdmin\Components\Fields\DateTimeField;

class DateTimeSearchField extends DateTimeField
{
    /**
     * @var string
     */
    public static $condition = '>=';

    /**
     * @param $value
     * @return Carbon
     */
    public static function transformValue($value)
    {
        return Carbon::create($value);
    }
}
