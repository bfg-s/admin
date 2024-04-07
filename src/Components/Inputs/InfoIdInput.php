<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

class InfoIdInput extends InfoInput
{
    /**
     * @var string|null
     */
    protected ?string $icon = 'fas fa-quote-right';

    /**
     * @param  string  $name
     * @param  string  $title
     * @param ...$params
     */
    public function __construct(string $name = 'id', string $title = 'admin.id', ...$params)
    {
        parent::__construct($name, $title, $params);
    }
}
