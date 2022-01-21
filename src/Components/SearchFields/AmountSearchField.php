<?php

namespace Lar\LteAdmin\Components\SearchFields;

use Lar\LteAdmin\Components\Fields\AmountField;

class AmountSearchField extends AmountField
{
    /**
     * @var string
     */
    public static $condition = '>=';
}
