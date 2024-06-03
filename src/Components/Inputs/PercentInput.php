<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Illuminate\View\View;

/**
 * Input admin panel for entering percentage.
 */
class PercentInput extends Input
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = 'fas fa-percent';

    /**
     * Method for creating an input field.
     *
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
            'digitsOptional' => false,
            'placeholder' => "0",
            'radixPoint' => ".",
            'groupSeparator' => "",
            'autoGroup' => false
        ]);

        return parent::field();
    }
}
