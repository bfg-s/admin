<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\SelectTagsInput;

class SelectTagsSearchField extends SelectTagsInput
{
    /**
     * @var string
     */
    public static string $condition = 'in';

    /**
     * After construct event.
     */
    protected function after_construct(): void
    {
        $this->nullable();
    }
}
