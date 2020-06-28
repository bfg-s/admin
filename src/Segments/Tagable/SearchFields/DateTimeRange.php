<?php

namespace Lar\LteAdmin\Segments\Tagable\SearchFields;

use Carbon\Carbon;

/**
 * Class DateTimeRange
 * @package Lar\LteAdmin\Segments\Tagable\SearchFields
 */
class DateTimeRange extends \Lar\LteAdmin\Segments\Tagable\Fields\DateTimeRange
{
    /**
     * @var string
     */
    static $condition = "between";

    /**
     * @param $value
     * @return array
     */
    static function transformValue ($value) {

        $value = explode(' - ', $value);

        return [
            Carbon::create($value[0]),
            Carbon::create($value[1])
        ];
    }
}