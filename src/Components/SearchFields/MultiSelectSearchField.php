<?php

namespace Lar\LteAdmin\Components\SearchFields;

use Lar\LteAdmin\Components\Fields\MultiSelectField;

class MultiSelectSearchField extends MultiSelectField
{
    /**
     * @var string
     */
    public static $condition = 'in';
}
