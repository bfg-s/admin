<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\ChecksInput;

class ChecksSearchInput extends ChecksInput
{
    /**
     * @var string
     */
    public static string $condition = 'in';
}
