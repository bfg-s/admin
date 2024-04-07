<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\AmountInput;

class AmountSearchInput extends AmountInput
{
    /**
     * @var string
     */
    public static string $condition = '>=';
}
