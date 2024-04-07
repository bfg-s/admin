<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;


class AmountInput extends Input
{
    /**
     * @var string|null
     */
    protected ?string $icon = 'fas fa-dollar-sign';

    /**
     * After construct event.
     * @return void
     */
    protected function after_construct(): void
    {
        $this->mask('currency', ['rightAlign' => false]);
    }
}
