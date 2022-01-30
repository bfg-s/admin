<?php

namespace LteAdmin\Components\SearchFields;

use LteAdmin\Components\Fields\SelectField;

class SelectSearchField extends SelectField
{
    /**
     * @var string
     */
    public static $condition = '=';

    /**
     * After construct event.
     */
    protected function after_construct()
    {
        $this->nullable();
    }
}
