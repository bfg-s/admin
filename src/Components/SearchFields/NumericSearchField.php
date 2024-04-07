<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\NumericInput;

class NumericSearchField extends NumericInput
{
    /**
     * @var string
     */
    public static string $condition = '=';
}
