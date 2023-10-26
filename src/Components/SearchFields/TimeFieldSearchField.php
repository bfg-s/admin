<?php

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\TimeInput;

class TimeFieldSearchField extends TimeInput
{
    /**
     * @var string
     */
    public static string $condition = '=';
}
