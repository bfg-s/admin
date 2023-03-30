<?php

namespace Admin\Components\SearchFields;

use Admin\Components\Fields\MultiSelectField;

class MultiSelectSearchField extends MultiSelectField
{
    /**
     * @var string
     */
    public static $condition = 'in';
}
