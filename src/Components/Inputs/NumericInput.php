<?php

namespace Admin\Components\Inputs;

use Illuminate\View\View;

class NumericInput extends Input
{
    /**
     * @var string|null
     */
    protected ?string $icon = 'fas fa-hashtag';

    /**
     * @var int
     */
    protected int $digits = 2;

    /**
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
     * @param  int  $digits
     * @return $this
     */
    public function setDigits(int $digits): static
    {
        $this->digits = $digits;

        return $this;
    }
}
