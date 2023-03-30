<?php

namespace Admin\Components\SearchFields;

use Admin\Components\Fields\TimeField;

class TimeFieldSearchField extends TimeField
{
    /**
     * @var string
     */
    public static $condition = '=';
}
