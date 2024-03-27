<?php

namespace Admin\Components\Inputs;

use Illuminate\View\View;

class PercentInput extends Input
{
    /**
     * @var string|null
     */
    protected ?string $icon = 'fas fa-percent';

    /**
     * @return View
     */
    public function field(): View
    {
        $this->mask([
            'rightAlign' => false,
            'alias' => "decimal",
            'integerDigits' => 3,
            'digits' => 2,
            'min' => 0,
            'max' => 100,
            //'suffix' => " %",
            'digitsOptional' => false,
            'placeholder' => "0",
            'radixPoint' => ".",
            'groupSeparator' => "",
            'autoGroup' => false
        ]);

        return parent::field();
    }
}
