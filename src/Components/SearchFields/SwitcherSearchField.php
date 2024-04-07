<?php

declare(strict_types=1);

namespace Admin\Components\SearchFields;

use Admin\Components\Inputs\SwitcherInput;

class SwitcherSearchField extends SwitcherInput
{
    /**
     * @var string
     */
    public static string $condition = '=';
}
