<?php

namespace Lar\LteAdmin\Components\SearchFields;

use Lar\LteAdmin\Components\Fields\SelectTagsField;

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
