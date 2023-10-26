<?php

namespace Admin\Components\Inputs;

class EmailInput extends Input
{
    /**
     * @var string
     */
    protected $type = 'email';

    /**
     * @var string|null
     */
    protected ?string $icon = 'fas fa-envelope';

    /**
     * After construct event.
     */
    protected function after_construct(): void
    {
        $this->email();
    }
}
