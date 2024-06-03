<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\SelectTagsInput;

/**
 * Admin panel search input for tag selector.
 */
class SelectTagsSearchField extends SelectTagsInput
{
    /**
     * Comparisons for the current field.
     *
     * @var string
     */
    public static string $condition = 'in';

    /**
     * After construct event.
     *
     * @return void
     */
    protected function after_construct(): void
    {
        $this->nullable();
    }
}
