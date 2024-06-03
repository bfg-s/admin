<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\SelectInput;

/**
 * Admin panel search input for data selector.
 */
class SelectSearchInput extends SelectInput
{
    /**
     * Comparisons for the current field.
     *
     * @var string
     */
    public static string $condition = '=';

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
