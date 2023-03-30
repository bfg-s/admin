<?php

namespace Admin\Components\SearchFields;

use Admin\Components\Fields\AmountField;

class AmountSearchField extends AmountField
{
    /**
     * @var string
     */
    public static $condition = '>=';
}
