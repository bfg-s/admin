<?php

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\SelectInput;

class SelectSearchInput extends SelectInput
{
    /**
     * @var string
     */
    public static string $condition = '=';

    /**
     * After construct event.
     */
    protected function after_construct(): void
    {
        $this->nullable();
    }
}
