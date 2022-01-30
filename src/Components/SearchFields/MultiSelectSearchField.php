<?php

namespace LteAdmin\Components\SearchFields;

use LteAdmin\Components\Fields\MultiSelectField;

class MultiSelectSearchField extends MultiSelectField
{
    /**
     * @var string
     */
    public static $condition = 'in';
}
