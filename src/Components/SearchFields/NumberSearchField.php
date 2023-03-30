<?php

namespace Admin\Components\SearchFields;

use Admin\Components\Fields\NumberField;

class NumberSearchField extends NumberField
{
    /**
     * @var string
     */
    public static $condition = '=';
}
