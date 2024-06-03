<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

/**
 * Input admin panel for entering mail.
 */
class EmailInput extends Input
{
    /**
     * HTML attribute of the input type.
     *
     * @var string
     */
    protected $type = 'email';

    /**
     * Admin panel input icon.
     *
     * @var string|null
     */
    protected ?string $icon = 'fas fa-envelope';

    /**
     * After construct event.
     *
     * @return void
     */
    protected function after_construct(): void
    {
        $this->email();
    }
}
