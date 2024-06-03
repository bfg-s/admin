<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Illuminate\View\View;

/**
 * Input admin panel for entering numbers with possible floating point.
 */
class NumericInput extends Input
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected string|null $icon = 'fas fa-hashtag';

    /**
     * Number of characters after floating point.
     *
     * @var int
     */
    protected int $digits = 2;

    /**
     * Method for creating an input field.
     *
     * @return View
     */
    public function field(): View
    {
        $this->mask('decimal', [
            'radixPoint' => '.',
            'digits' => $this->digits,
            'repeat' => 10,
            'rightAlign' => false
        ]);

        return parent::field();
    }

    /**
     * Set the number of characters after floating point.
     *
     * @param  int  $digits
     * @return $this
     */
    public function setDigits(int $digits): static
    {
        $this->digits = $digits;

        return $this;
    }
}
