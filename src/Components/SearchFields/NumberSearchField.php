<?php

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\NumberInput;

class NumberSearchField extends NumberInput
{
    /**
     * @var string
     */
    public static string $condition = '=';
}
