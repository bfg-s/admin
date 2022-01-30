<?php

namespace LteAdmin\Components\SearchFields;

use LteAdmin\Components\Fields\AmountField;

class AmountSearchField extends AmountField
{
    /**
     * @var string
     */
    public static $condition = '>=';
}
