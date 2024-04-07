<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\DateRangeInput;
use Carbon\Carbon;

class DateRangeSearchInput extends DateRangeInput
{
    /**
     * @var string
     */
    public static string $condition = 'between';

    /**
     * @param $value
     * @return array
     */
    public static function transformValue($value): array
    {
        $value = explode(' - ', $value);

        return [
            Carbon::create($value[0])->startOfDay(),
            Carbon::create($value[1])->endOfDay(),
        ];
    }
}
