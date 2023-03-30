<?php

namespace Admin\Components\SearchFields;

use Admin\Components\Fields\SelectTagsField;

class SelectTagsSearchField extends SelectTagsField
{
    /**
     * @var string
     */
    public static $condition = 'in';

    /**
     * After construct event.
     */
    protected function after_construct()
    {
        $this->nullable();
    }
}
