<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

/**
 * Input the admin panel to enter the amount.
 */
class AmountInput extends Input
{
    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = 'fas fa-dollar-sign';

    /**
     * After construct event.
     *
     * @return void
     */
    protected function after_construct(): void
    {
        $this->mask('currency', [
            'rightAlign' => false,
            'radixPoint' => ".",
            'groupSeparator' => "",
        ]);
    }
}
