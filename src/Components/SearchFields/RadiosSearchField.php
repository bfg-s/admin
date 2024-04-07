<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\RadiosInput;

class RadiosSearchField extends RadiosInput
{
    /**
     * @var string
     */
    public static string $condition = '=';
}
